<?php
session_start();
require "dbconnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill all fields.";
        header("Location: ../index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // DEBUGGING: Check if user exists
    if (!$user) {
        $_SESSION['error'] = "User not found. Check your email.";
        header("Location: ../index.php");
        exit();
    }

    // DEBUGGING: Check password verification
    $passwordMatch = password_verify($password, $user["password"]);
    
    if (!$passwordMatch) {
        // Log the issue (remove this after debugging)
        error_log("Password mismatch for email: " . $email);
        $_SESSION['error'] = "Invalid password.";
        header("Location: ../index.php");
        exit();
    }

    // If we get here, login is successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['name']    = $user['full_name'];

    if ($user['role'] === 'superadmin') {
        header("Location: ../admin/admin_dashboard.php");
    } else {
        header("Location: ../department/dept_dashboard.php");
    }
    exit();
}