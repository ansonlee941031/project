<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require '../includes/header.php'; 

?>

<div class="container mt-4">
    <h1>管理員首頁</h1>
    <p>哈囉，<?php echo htmlspecialchars($_SESSION['name']); ?>！</p>
    <a href="../logout.php">登出</a> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>