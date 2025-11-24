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

if ($stmt === false) {
    error_log("Database Prepare Error: " . mysqli_error($conn));
    header('Location: index.php?error=db_fail');
    exit;
}

mysqli_stmt_bind_param($stmt, "ss", $username, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);


if ($user && $user['password'] === $password) {  
    $_SESSION['user_id'] = $user['id'] ?? $user['username']; 
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role']; 

    
    $admin_type = '';
    if ($user['role'] === 'admin') {
        if (strpos($user['username'], 'parcel') !== false || strpos($user['username'], 'root') !== false) {
            $admin_type = 'parcel_admin'; 
        } elseif (strpos($user['username'], 'asset') !== false || strpos($user['username'], 'public') !== false) {
            $admin_type = 'asset_admin'; 
        } else {
            $admin_type = 'all_admin'; 
        }
        $_SESSION['admin_type'] = $admin_type; 

        // 3. 導向管理員儀表板
        header('Location: admin/dashboard.php'); 
        
    } else {
        // 導向住戶儀表板
        header('Location: resident/dashboard.php');
    }
    exit;
} else {
    // 登入失敗
    header('Location: index.php?error=1');
    exit;
}
