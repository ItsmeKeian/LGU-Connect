<?php
ob_start();
require "../auth_check.php";
require "../dbconnect.php";
ob_end_clean();

header('Content-Type: application/json');

requireSuperAdmin();

$id        = intval($_POST['id']       ?? 0);
$full_name = trim($_POST['full_name']  ?? '');
$username  = trim($_POST['username']   ?? '');
$email     = trim($_POST['email']      ?? '');
$password  = trim($_POST['password']   ?? '');
$role      = trim($_POST['role']       ?? 'dept_user');
$dept      = trim($_POST['department'] ?? '') ?: null;

if (!$full_name || !$username || !$email) {
    echo json_encode(['success' => false, 'message' => 'Name, username, and email are required.']);
    exit();
}

if ($role === 'dept_user' && !$dept) {
    echo json_encode(['success' => false, 'message' => 'Please assign a department for this user.']);
    exit();
}

if ($role === 'superadmin') $dept = null;

// ── Duplicate check ──
if ($id) {
    $chk = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $chk->execute([$username, $email, $id]);
} else {
    $chk = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $chk->execute([$username, $email]);
}

if ($chk->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    exit();
}

try {
    if ($id) {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, password=?, role=?, department=? WHERE id=?");
            $stmt->execute([$full_name, $username, $email, $hashed, $role, $dept, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, role=?, department=? WHERE id=?");
            $stmt->execute([$full_name, $username, $email, $role, $dept, $id]);
        }
    } else {
        if (!$password) {
            echo json_encode(['success' => false, 'message' => 'Password is required for new users.']);
            exit();
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, role, department) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $username, $email, $hashed, $role, $dept]);
    }

    echo json_encode(['success' => true, 'message' => 'User saved successfully.']);

} catch (Exception $e) {
    // ✅ Show exact error — para makita natin kung ano talaga
    echo json_encode([
        'success' => false,
        'message' => 'DB Error: ' . $e->getMessage(),
        'code'    => $e->getCode()
    ]);
}