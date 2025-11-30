<?php
session_start();
require 'config/db.php'; 
require 'includes/header.php'; 

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('僅管理員可進入公物管理頁面。');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$message = '';


if (isset($_POST['lend_item'])) {
    $public_id = (int)($_POST['public_id'] ?? 0);
    $borrower_name = htmlspecialchars($_POST['borrower_name'] ?? '');
    $expected_return_time = $_POST['expected_return_time'] ?? '';

    // 確保物品目前狀態為 'Y' 
    $sql_check = "SELECT state, public_name FROM public WHERE public_id = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $public_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $item = mysqli_fetch_assoc($result_check);

    if ($item && $item['state'] === 'Y') {
        // 更新狀態：Y -> N (已借出)
        // 更新借用時間、預計歸還時間、借用人
        $sql_update = "UPDATE public SET 
                       state = 'N', 
                       borrow_time = NOW(), 
                       expected_return_time = ?, 
                       borrower_name = ? 
                       WHERE public_id = ?";
        
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ssi", $expected_return_time, $borrower_name, $public_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $message = "success: 公物【{$item['public_name']}】借出成功，狀態已更新為『已借出 (N)』！";
        } else {
            $message = "error: 借出失敗，資料庫寫入錯誤。";
        }
    } elseif ($item && $item['state'] === 'N') {
        $message = "warning: 該公物目前已被借出。";
    } else {
        $message = "error: 找不到該公物或 ID 錯誤。";
    }
}

//  處理公物歸還  N -> Y
if (isset($_POST['return_item'])) {
    $public_id = (int)($_POST['public_id'] ?? 0);

    //  檢查狀態是否為 'N' (已借出)
    $sql_check = "SELECT state, public_name FROM public WHERE public_id = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $public_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $item = mysqli_fetch_assoc($result_check);
    
    if ($item && $item['state'] === 'N') {
        //  更新狀態：N -> Y 
        $sql_update = "UPDATE public SET 
                       state = 'Y', 
                       borrow_time = NULL, 
                       expected_return_time = NULL, 
                       borrower_name = NULL 
                       WHERE public_id = ?";
        
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "i", $public_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $message = "success: 公物【{$item['public_name']}】歸還成功，狀態已更新為『在庫 (Y)』！";
        } else {
            $message = "error: 歸還失敗，資料庫寫入錯誤。";
        }
    } elseif ($item && $item['state'] === 'Y') {
        $message = "warning: 該公物目前在庫中，無需歸還。";
    } else {
        $message = "error: 找不到該公物或 ID 錯誤。";
    }
}

?>

<div class="container mt-4">
    <h2>🛒 公物借出/歸還作業 (生產管理核心)</h2>
    <p><a href="admin/dashboard.php">← 返回管理儀表板</a></p>

    <?php if ($message): ?>
        <div class="alert <?= (strpos($message, 'success') !== false) ? 'alert-success' : (strpos($message, 'warning') !== false ? 'alert-warning' : 'alert-danger'); ?>" role="alert">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">1. 公物借出登記</div>
                <div class="card-body">
                    <p class="card-text">將物品狀態從 **在庫(Y)** 轉為 **已借出(N)**</p>
                    <form action="" method="POST">
                        <label class="form-label">公物 ID (public_id):</label>
                        <input type="number" name="public_id" class="form-control mb-2" required>
                        <label class="form-label">借用人姓名:</label>
                        <input type="text" name="borrower_name" class="form-control mb-2" required>
                        <label class="form-label">預計歸還日期:</label>
                        <input type="date" name="expected_return_time" class="form-control mb-3" required>
                        <button type="submit" name="lend_item" class="btn btn-success w-100">確認借出</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white fw-bold">2. 公物歸還登記</div>
                <div class="card-body">
                    <p class="card-text">將物品狀態從 **已借出(N)** 轉回 **在庫(Y)**</p>
                    <form action="" method="POST">
                        <label class="form-label">歸還的公物 ID:</label>
                        <input type="number" name="public_id" class="form-control mb-3" required>
                        <button type="submit" name="return_item" class="btn btn-primary w-100">確認歸還</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
