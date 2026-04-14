<?php

require "../dbconnect.php";

header('Content-Type: application/json');

$dept_code = strtoupper(trim($_GET['dept'] ?? ''));

// ── 1. Is feedback open? ──
$is_open = $conn->query(
    "SELECT setting_value FROM settings WHERE setting_key='feedback_open' LIMIT 1"
)->fetchColumn();

// ── 2. LGU settings ──
$rows = $conn->query(
    "SELECT setting_key, setting_value FROM settings WHERE setting_group = 'lgu'"
)->fetchAll(PDO::FETCH_ASSOC);

$lgu_raw = [];
foreach ($rows as $r) { $lgu_raw[$r['setting_key']] = $r['setting_value']; }

$lgu = [
    'name'    => $lgu_raw['lgu_name']    ?? 'Municipality of San Julian',
    'address' => $lgu_raw['lgu_address'] ?? 'San Julian, Eastern Samar',
];

// ── 3. Specific department (from URL ?dept=CODE) ──
$department = null;
if ($dept_code) {
    $stmt = $conn->prepare(
        "SELECT code, name, head, description FROM departments WHERE code = ? AND status = 'active' LIMIT 1"
    );
    $stmt->execute([$dept_code]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// ── 4. All active departments (for dropdown) ──
$all_depts = $conn->query(
    "SELECT code, name FROM departments WHERE status = 'active' ORDER BY name ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// ── 5. SQD Questions (ARTA standard) ──
$sqd_questions = [
    ['key' => 'sqd0', 'question' => "I am aware of the office's Citizens Charter."],
    ['key' => 'sqd1', 'question' => "I spent an acceptable amount of time waiting for the service."],
    ['key' => 'sqd2', 'question' => "The office followed the transaction time as indicated in the Citizens Charter."],
    ['key' => 'sqd3', 'question' => "The officer/employee who attended to me was helpful, courteous, and respectful."],
    ['key' => 'sqd4', 'question' => "I paid the required fees for the service and nothing more."],
    ['key' => 'sqd5', 'question' => "The officer/employee who attended to me followed the prescribed process."],
    ['key' => 'sqd6', 'question' => "The office's service quality is at par with the standard set by the Citizens Charter."],
    ['key' => 'sqd7', 'question' => "The office was able to deliver the service in a timely manner."],
    ['key' => 'sqd8', 'question' => "I am satisfied with the service I received."],
];

echo json_encode([
    'success'       => true,
    'is_open'       => $is_open !== '0',
    'lgu'           => $lgu,
    'department'    => $department,
    'all_depts'     => $all_depts,
    'sqd_questions' => $sqd_questions,
]);