<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Not logged in — redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Define a helper for easy role checking
define('IS_SUPERADMIN', $_SESSION['role'] === 'superadmin');
define('IS_DEPT_USER',  $_SESSION['role'] === 'dept_user');
define('CURRENT_DEPT',  $_SESSION['department'] ?? '');
define('CURRENT_USER',  $_SESSION['name'] ?? 'User');