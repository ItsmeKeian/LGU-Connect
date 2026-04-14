<?php

require "../dbconnect.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// ── Check if feedback is open ──
$open = $conn->query("SELECT setting_value FROM settings WHERE setting_key='feedback_open' LIMIT 1")->fetchColumn();
if ($open === '0') {
    echo json_encode(['success' => false, 'message' => 'Feedback collection is currently closed.']);
    exit;
}

// ── Sanitize inputs ──
$dept_code      = strtoupper(trim($_POST['dept_code']      ?? ''));
$respondent_type= trim($_POST['respondent_type'] ?? 'citizen');
$sex            = trim($_POST['sex']             ?? '');
$age_group      = trim($_POST['age_group']       ?? '');
$rating         = (int)($_POST['rating']         ?? 0);
$comment        = trim($_POST['comment']         ?? '');
$suggestions    = trim($_POST['suggestions']     ?? '');

// SQD scores
$sqd = [];
for ($i = 0; $i <= 8; $i++) {
    $sqd[$i] = (int)($_POST["sqd{$i}"] ?? 0);
}

// ── Validate required fields ──
$errors = [];

if (empty($dept_code)) $errors[] = 'Department is required.';
if (!in_array($respondent_type, ['citizen','employee','business_owner','other'])) $errors[] = 'Invalid respondent type.';
if (!in_array($sex, ['male','female','prefer_not_to_say'])) $errors[] = 'Sex is required.';
if (!in_array($age_group, ['below_18','18_30','31_45','46_60','above_60'])) $errors[] = 'Age group is required.';
if ($rating < 1 || $rating > 5) $errors[] = 'Overall rating is required.';
foreach ($sqd as $i => $val) {
    if ($val < 1 || $val > 5) $errors[] = "SQD{$i} rating is required.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Verify department exists ──
$deptStmt = $conn->prepare("SELECT name FROM departments WHERE code = ? AND status = 'active' LIMIT 1");
$deptStmt->execute([$dept_code]);
$dept = $deptStmt->fetch(PDO::FETCH_ASSOC);

if (!$dept) {
    echo json_encode(['success' => false, 'message' => 'Invalid or inactive department.']);
    exit;
}

// ── Insert feedback ──
try {
    $stmt = $conn->prepare("
        INSERT INTO feedback
            (department_code, respondent_type, sex, age_group,
             rating,
             sqd0, sqd1, sqd2, sqd3, sqd4, sqd5, sqd6, sqd7, sqd8,
             comment, suggestions, submitted_at)
        VALUES
            (?, ?, ?, ?,
             ?,
             ?, ?, ?, ?, ?, ?, ?, ?, ?,
             ?, ?, NOW())
    ");

    $stmt->execute([
        $dept_code, $respondent_type, $sex, $age_group,
        $rating,
        $sqd[0], $sqd[1], $sqd[2], $sqd[3], $sqd[4],
        $sqd[5], $sqd[6], $sqd[7], $sqd[8],
        $comment ?: null,
        $suggestions ?: null,
    ]);

    echo json_encode([
        'success'   => true,
        'message'   => 'Feedback submitted successfully.',
        'dept_name' => $dept['name'],
        'id'        => $conn->lastInsertId(),
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}