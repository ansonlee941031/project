<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$path = "../"; 
require '../includes/header.php'; 

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>管理員首頁</h1>
            <h5 class="text-muted">哈囉，<?php echo htmlspecialchars($_SESSION['name']); ?>！</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">
                    包裹管理
                </div>
                <div class="card-body">
                    <h5 class="card-title">待領取與歷史包裹</h5>
                    <p class="card-text">
                        <strong>新增包裹記錄 / 學生簽收包裹</strong>
                    </p>
                    <a href="../package.php" class="btn btn-primary w-100">前往包裹管理</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">
                    公物管理
                </div>
                <div class="card-body">
                    <h5 class="card-title">借用與歸還狀態</h5>
                    <p class="card-text">
                        <strong>管理公物清單 / 查看借出狀況 / 處理租借歸還登記</strong>
                    </p>
                    <a href="../public.php" class="btn btn-primary w-100">前往公物管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">
                    包裹公物狀態
                </div>
                <div class="card-body">
                    <h5 class="card-title">包裹與公物租借狀態</h5>
                    <p class="card-text">
                        <strong>查看包裹清單 / 查看公物租借狀況</strong>
                    </p>
                    <a href="../state.php" class="btn btn-primary w-100">前往包裹公物狀態頁</a>
                </div>
            </div>
        </div>
    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>