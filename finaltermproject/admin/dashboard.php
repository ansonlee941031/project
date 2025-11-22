<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<h1>管理員首頁</h1>
<p>哈囉，<?php echo htmlspecialchars($_SESSION['name']); ?>！</p>
<a href="../logout.php">登出</a>