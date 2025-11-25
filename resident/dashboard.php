<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: ../index.php');
    exit;

    require 'includes/header.php';

}
?>
<h1>住民首頁</h1>
<p>哈囉，<?php echo htmlspecialchars($_SESSION['name']); ?>！</p>
<a href="../logout.php">登出</a>

 