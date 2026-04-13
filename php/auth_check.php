<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // ✅ Check if AJAX request — kung oo, JSON response; kung hindi, redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit();
    }
    header("Location: ../index.php");
    exit();
}

define('IS_SUPERADMIN',   $_SESSION['role'] === 'superadmin');
define('IS_DEPT_USER',    $_SESSION['role'] === 'dept_user');
define('CURRENT_USER',    $_SESSION['name']       ?? 'User');
define('CURRENT_DEPT',    $_SESSION['department'] ?? '');
define('CURRENT_ROLE',    $_SESSION['role']       ?? '');
define('CURRENT_USER_ID', $_SESSION['user_id']    ?? null);

function requireSuperAdmin() {
    if ($_SESSION['role'] !== 'superadmin') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit();
        }
        header('Location: ../department/dept_dashboard.php');
        exit();
    }
}

function requireDeptUser() {
    if ($_SESSION['role'] !== 'dept_user') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit();
        }
        header('Location: ../admin/admin_dashboard.php');
        exit();
    }
}