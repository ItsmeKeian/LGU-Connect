<?php
require "../auth_check.php";
require "../dbconnect.php";
requireSuperAdmin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? 'fetch';

try {

    if ($action === 'clear') {
        $conn->exec("DELETE FROM export_logs");
        echo json_encode(['success' => true, 'message' => 'Export history cleared.']);
        exit;
    }

    // Fetch latest 30 export logs
    $stmt = $conn->prepare("
        SELECT
            id,
            exported_by,
            export_type,
            export_format,
            dept_code,
            dept_name,
            date_from,
            date_to,
            record_count,
            DATE_FORMAT(created_at, '%b %d, %Y') AS export_date,
            DATE_FORMAT(created_at, '%h:%i %p')  AS export_time,
            created_at
        FROM export_logs
        ORDER BY created_at DESC
        LIMIT 30
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'logs' => $logs]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}