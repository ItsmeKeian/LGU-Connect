<?php

require "../auth_check.php";
require "../dbconnect.php";

header('Content-Type: application/json');


if (!IS_SUPERADMIN && !IS_DEPT_USER) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}


if (IS_DEPT_USER) {
    $dept_code = CURRENT_DEPT; // 
} else {
    $dept_code = (isset($_POST['dept_id']) && $_POST['dept_id'] !== '') ? $_POST['dept_id'] : null;
}

$date_from = $_POST['date_from'] ?? date('Y-m-01');
$date_to   = $_POST['date_to']   ?? date('Y-m-t');
$incl_dept = (int)($_POST['incl_dept'] ?? 1);
$incl_raw  = (int)($_POST['incl_raw']  ?? 0);

$date_from_dt = date('Y-m-d', strtotime($date_from));
$date_to_dt   = date('Y-m-d', strtotime($date_to));
$period_label = date('M j, Y', strtotime($date_from_dt)) . ' – ' . date('M j, Y', strtotime($date_to_dt));

// ── WHERE clause ──
$where  = "WHERE DATE(f.submitted_at) BETWEEN :from AND :to";
$params = [':from' => $date_from_dt, ':to' => $date_to_dt];

if ($dept_code) {
    $where               .= " AND f.department_code = :dept_code";
    $params[':dept_code'] = $dept_code;
}

try {

    // ── 1. Overall Summary ──
    $stmt = $conn->prepare("
        SELECT
            COUNT(*)                                                            AS total_responses,
            ROUND(AVG(f.rating), 2)                                            AS avg_rating,
            ROUND(
                SUM(CASE WHEN f.rating >= 4 THEN 1 ELSE 0 END) * 100.0
                / NULLIF(COUNT(*), 0), 1
            )                                                                  AS satisfaction_rate,
            COUNT(DISTINCT f.department_code)                                  AS dept_count,
            SUM(CASE WHEN f.rating = 5 THEN 1 ELSE 0 END)                     AS cnt_5,
            SUM(CASE WHEN f.rating = 4 THEN 1 ELSE 0 END)                     AS cnt_4,
            SUM(CASE WHEN f.rating = 3 THEN 1 ELSE 0 END)                     AS cnt_3,
            SUM(CASE WHEN f.rating = 2 THEN 1 ELSE 0 END)                     AS cnt_2,
            SUM(CASE WHEN f.rating = 1 THEN 1 ELSE 0 END)                     AS cnt_1,
            ROUND(AVG(f.sqd0), 2) AS avg_sqd0, ROUND(AVG(f.sqd1), 2) AS avg_sqd1,
            ROUND(AVG(f.sqd2), 2) AS avg_sqd2, ROUND(AVG(f.sqd3), 2) AS avg_sqd3,
            ROUND(AVG(f.sqd4), 2) AS avg_sqd4, ROUND(AVG(f.sqd5), 2) AS avg_sqd5,
            ROUND(AVG(f.sqd6), 2) AS avg_sqd6, ROUND(AVG(f.sqd7), 2) AS avg_sqd7,
            ROUND(AVG(f.sqd8), 2) AS avg_sqd8,
            SUM(CASE WHEN f.respondent_type = 'citizen'        THEN 1 ELSE 0 END) AS cnt_citizen,
            SUM(CASE WHEN f.respondent_type = 'employee'       THEN 1 ELSE 0 END) AS cnt_employee,
            SUM(CASE WHEN f.respondent_type = 'business_owner' THEN 1 ELSE 0 END) AS cnt_business,
            SUM(CASE WHEN f.respondent_type = 'other'          THEN 1 ELSE 0 END) AS cnt_other
        FROM feedback f
        $where
    ");
    $stmt->execute($params);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    $summary['period_label']      = $period_label;
    $summary['satisfaction_rate'] = $summary['satisfaction_rate'] ?? '0.0';
    $summary['avg_rating']        = $summary['avg_rating']        ?? '0.00';

    // No data
    if ((int)$summary['total_responses'] === 0) {
        echo json_encode([
            'success'         => true,
            'summary'         => ['total_responses'=>0,'avg_rating'=>'0.00','satisfaction_rate'=>'0.0','dept_count'=>0,'period_label'=>$period_label],
            'departments'     => [],
            'feedbacks'       => [],
            'recent_comments' => [],
            'by_type'         => [],
            'by_age'          => [],
        ]);
        exit;
    }

    // ── 2. Per-Dept Breakdown (superadmin only) ──
    $departments = [];
    if ($incl_dept && IS_SUPERADMIN) {
        $stmt2 = $conn->prepare("
            SELECT
                f.department_code AS dept_id,
                COALESCE(d.name, f.department_code) AS dept_name,
                d.head AS dept_head,
                COUNT(f.id) AS total_responses,
                ROUND(AVG(f.rating), 2) AS avg_rating,
                ROUND(SUM(CASE WHEN f.rating >= 4 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(f.id),0), 1) AS satisfaction_rate,
                ROUND(AVG(f.sqd0),2) AS avg_sqd0, ROUND(AVG(f.sqd1),2) AS avg_sqd1,
                ROUND(AVG(f.sqd2),2) AS avg_sqd2, ROUND(AVG(f.sqd3),2) AS avg_sqd3,
                ROUND(AVG(f.sqd4),2) AS avg_sqd4, ROUND(AVG(f.sqd5),2) AS avg_sqd5,
                ROUND(AVG(f.sqd6),2) AS avg_sqd6, ROUND(AVG(f.sqd7),2) AS avg_sqd7,
                ROUND(AVG(f.sqd8),2) AS avg_sqd8
            FROM feedback f
            LEFT JOIN departments d ON d.code = f.department_code
            $where
            GROUP BY f.department_code, d.name, d.head
            ORDER BY total_responses DESC
        ");
        $stmt2->execute($params);
        $departments = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── 3. Raw Feedback ──
    $feedbacks = [];
    if ($incl_raw) {
        $stmt3 = $conn->prepare("
            SELECT f.id, COALESCE(d.name, f.department_code) AS dept_name,
                   f.department_code, f.rating, f.comment, f.suggestions,
                   f.respondent_type, f.sex, f.age_group,
                   DATE_FORMAT(f.submitted_at,'%b %d, %Y') AS submitted_at
            FROM feedback f
            LEFT JOIN departments d ON d.code = f.department_code
            $where ORDER BY f.submitted_at DESC LIMIT 500
        ");
        $stmt3->execute($params);
        $feedbacks = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── 4. Recent Comments ──
    $commentsStmt = $conn->prepare("
        SELECT f.rating, f.comment, f.respondent_type,
               DATE_FORMAT(f.submitted_at,'%b %d, %Y') AS submitted_at
        FROM feedback f $where
        AND f.comment IS NOT NULL AND f.comment != ''
        ORDER BY f.submitted_at DESC LIMIT 10
    ");
    $commentsStmt->execute($params);
    $recent_comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

    // ── 5. By Respondent Type ──
    $typeStmt = $conn->prepare("
        SELECT respondent_type, COUNT(*) AS total
        FROM feedback f $where
        GROUP BY respondent_type ORDER BY total DESC
    ");
    $typeStmt->execute($params);
    $by_type = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

    // ── 6. By Age Group ──
    $ageStmt = $conn->prepare("
        SELECT age_group, COUNT(*) AS total
        FROM feedback f $where
        GROUP BY age_group ORDER BY total DESC
    ");
    $ageStmt->execute($params);
    $by_age = $ageStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'         => true,
        'summary'         => $summary,
        'departments'     => $departments,
        'feedbacks'       => $feedbacks,
        'recent_comments' => $recent_comments,
        'by_type'         => $by_type,
        'by_age'          => $by_age,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}