<?php
session_start();

// 權限檢查
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// 引入資料庫連線，用於圖表數據查詢
require '../config/db.php'; 


$title = "管理員儀表板 | 數據總覽"; 
// ****************************

// 引入 header 
$path = "../"; 
require '../includes/header.php'; 

// --- 1. 數據查詢與計算 ---
// A. 包裹狀態統計
$totalPackages = 0;
$receivedPackages = 0;

$sqlPackageTotal = "SELECT COUNT(*) AS total FROM package";
$sqlPackageReceived = "SELECT COUNT(*) AS received FROM package WHERE state = 'Y'";

$resTotal = mysqli_query($conn, $sqlPackageTotal);
if ($resTotal) {
    $totalPackages = mysqli_fetch_assoc($resTotal)['total'];
}

$resReceived = mysqli_query($conn, $sqlPackageReceived);
if ($resReceived) {
    $receivedPackages = mysqli_fetch_assoc($resReceived)['received'];
}

$notReceivedPackages = $totalPackages - $receivedPackages;

// B. 公物狀態統計
$totalPublic = 0;
$borrowedPublic = 0;

$sqlPublicTotal = "SELECT COUNT(*) AS total FROM public";
$sqlPublicBorrowed = "SELECT COUNT(*) AS borrowed FROM public WHERE state = 'N'";

$resPublicTotal = mysqli_query($conn, $sqlPublicTotal);
if ($resPublicTotal) {
    $totalPublic = mysqli_fetch_assoc($resPublicTotal)['total'];
}

$resPublicBorrowed = mysqli_query($conn, $sqlPublicBorrowed);
if ($resPublicBorrowed) {
    $borrowedPublic = mysqli_fetch_assoc($resPublicBorrowed)['borrowed'];
}

$availablePublic = $totalPublic - $borrowedPublic;

 JavaScript 的數據 
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>管理員首頁</h1>
            <h5 class="text-muted">哈囉，<?php echo htmlspecialchars($_SESSION['name']); ?>！</h5>
        </div>
        <div class="text-end">
            <p class="mb-0"> **包裹總數：** <?= $totalPackages; ?> 件</p>
            <p class="mb-0"> **公物總數：** <?= $totalPublic; ?> 件</p>
        </div>
    </div>

    <h2 class="mb-3">系統數據總覽</h2>
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm h-100 p-3">
                <h5 class="card-title text-center">包裹領取率</h5>
                <canvas id="packageChart" style="max-height: 350px;"></canvas>
                <p class="mt-2 text-muted text-center">已領取：<?= $receivedPackages; ?> 件 / 未領取：<?= $notReceivedPackages; ?> 件</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100 p-3">
                <h5 class="card-title text-center">公物借出率</h5>
                <canvas id="publicChart" style="max-height: 350px;"></canvas>
                <p class="mt-2 text-muted text-center">已借出：<?= $borrowedPublic; ?> 件 / 在庫：<?= $availablePublic; ?> 件</p>
            </div>
        </div>
    </div>
    
    <h2 class="mb-3">功能導航</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">包裹管理</div>
                <div class="card-body">
                    <h5 class="card-title">待領取與歷史包裹</h5>
                    <p class="card-text"><strong>新增包裹記錄 / 學生簽收包裹</strong></p>
                    <a href="../package.php" class="btn btn-primary w-100">前往包裹管理</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">公物管理</div>
                <div class="card-body">
                    <h5 class="card-title">借用與歸還狀態</h5>
                    <p class="card-text"><strong>管理公物清單 / 處理租借歸還登記</strong></p>
                    <a href="../public.php" class="btn btn-primary w-100">前往公物管理</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header custom-bg text-white fw-bold">包裹公物狀態</div>
                <div class="card-body">
                    <h5 class="card-title">包裹與公物租借狀態</h5>
                    <p class="card-text"><strong>查看包裹清單 / 查看公物租借狀況</strong></p>
                    <a href="../state.php" class="btn btn-primary w-100">前往包裹公物狀態頁</a>
                </div>
            </div>
        </div>
    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// --- 1. PHP 變數轉為 JavaScript 變數 
const received = <?= $receivedPackages; ?>;
const notReceived = <?= $notReceivedPackages; ?>;
const borrowed = <?= $borrowedPublic; ?>;
const available = <?= $availablePublic; ?>;

// --- 2. 包裹領取率圓餅圖繪製 ---
const packageCtx = document.getElementById('packageChart');
new Chart(packageCtx, {
    type: 'pie',
    data: {
        labels: ['已領取', '未領取'],
        datasets: [{
            data: [received, notReceived],
            backgroundColor: ['#4CAF50', '#FFC107'], // 綠色/黃色
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        // 顯示百分比和實際數量
                        label += context.formattedValue + ' (' + context.raw + ' 件)'; 
                        return label;
                    }
                }
            }
        }
    }
});

// --- 3. 公物借出率圓餅圖繪製 ---
const publicCtx = document.getElementById('publicChart');
new Chart(publicCtx, {
    type: 'pie',
    data: {
        labels: ['已借出', '在庫'],
        datasets: [{
            data: [borrowed, available],
            backgroundColor: ['#E91E63', '#2196F3'], // 粉紅/藍色
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.formattedValue + ' (' + context.raw + ' 件)';
                        return label;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
