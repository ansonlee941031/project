<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('僅管理員可進入公物管理頁面。');
            window.location.href = 'resident/dashboard.php';
          </script>";
    exit;
}

$path = ""; 
require 'includes/header.php'; 
?>