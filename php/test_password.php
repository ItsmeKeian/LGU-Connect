<?php
require "dbconnect.php";

$newPassword = 'admin123'; // Your new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$email = 'admin@sanjulian.gov.ph'; // Your admin email

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hashedPassword, $email]);

echo "✅ Password updated successfully!<br>";
echo "Email: " . $email . "<br>";
echo "New Password: " . $newPassword . "<br>";
echo "Hash: " . $hashedPassword;
?>