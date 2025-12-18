<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "management_system";
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}
date_default_timezone_set('Asia/Taipei');
mysqli_set_charset($conn, "utf8mb4");
?>  