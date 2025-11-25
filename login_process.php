<?php
session_start();
require 'config/db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if ($username === '' || $password === '' || ($role !== 'admin' && $role !== 'resident')) {
    header('Location: index.php?error=1');
    exit;
}

$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && $user['password'] === $password) {  
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: resident/dashboard.php');
    }
    exit;
} else {
    header('Location: index.php?error=1');
    exit;
}a
 