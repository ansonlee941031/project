
<?php
session_start();
$title=$title??"活動系統";
function nav_active($file) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $file ? ' active' : '';
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    
    <link rel="stylesheet" href="custom.css" />
</head>

<body class="bg-light">
  <nav class="navbar navbar-expand-lg custom-bg">
    <div class="container">
      <a class="navbar-brand" href="activity.php">包裹/公物系統</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link<?=nav_active('pack.php')?>" href="pack.php">包裹管理</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?=nav_active('property.php')?>" href="property.php">公物管理</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?=nav_active('job.php')?>" href="job.php">包裹公物狀態</a>
          </li>
        </ul>

        <ul class="navbar-nav mb-2 mb-lg-0">
        <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if(isset($_SESSION['account'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">登出</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">登入</a>
          </li>
        <?php endif; ?>
      </ul>
      </div>
    </div>
  </nav>
