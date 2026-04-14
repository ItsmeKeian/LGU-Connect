<?php

require "../auth_check.php";
require "../dbconnect.php";

header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT
            d.*,
            COUNT(f.id)                                                         AS feedback_count,
            ROUND(AVG(f.rating), 2)                                            AS avg_rating,
            ROUND(
                SUM(CASE WHEN f.rating >= 4 THEN 1 ELSE 0 END) * 100.0
                / NULLIF(COUNT(f.id), 0), 1
            )                                                                  AS satisfaction_rate
        FROM departments d
        LEFT JOIN feedback f ON f.department_code = d.code
        GROUP BY d.id
        ORDER BY d.name ASC
    ");

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ NO foreach loop here — real data is preserved as-is

    echo json_encode(['success' => true, 'data' => $departments]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}