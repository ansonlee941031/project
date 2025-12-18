<?php
// 載入必要的套件 (Composer)
require_once __DIR__ . '/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// 載入.env設定檔(讀取帳密)
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {

}

session_start();
require 'config/db.php';

// 權限檢查
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('僅管理員可進入包裹管理頁面。');
            window.location.href = 'resident/dashboard.php';
          </script>";
    exit;
}

$path = ""; 
require 'includes/header.php'; 

$message = '';

// 處理表單提交 

// 左側 新增包裹 (抵達登記 + 圖片 + Email)
if (isset($_POST['add_package'])) {
    $student_name = htmlspecialchars($_POST['student_name'] ?? '');
    $student_id = (int)($_POST['student_id'] ?? 0);
    $arrive_time = $_POST['arrive_time'] ?? date('Y-m-d H:i:s');
    
    // 圖片上傳處理
    $image_path = NULL; 
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $filename = time() . "_" . basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $image_path = $target_file; 
        } else {
            $message = "warning: 圖片上傳失敗，但仍會嘗試新增資料。";
        }
    }

    // 資料庫寫入
    $sql_insert = "INSERT INTO package (student_name, student_id, arrive_time, image_path, state, receive_time) 
                   VALUES (?, ?, ?, ?, 'N', NULL)";
    
    $stmt = mysqli_prepare($conn, $sql_insert);

    if ($stmt === false) {
        die("資料庫錯誤 (Prepare Failed): " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "siss", $student_name, $student_id, $arrive_time, $image_path);

    if (mysqli_stmt_execute($stmt)) {
        $new_id = mysqli_insert_id($conn); 
        $success_msg = "success: 包裹登記成功！系統編號 ID 為【{$new_id}】。";
        
        // 圖片預覽
        if ($image_path) {
            $success_msg .= "<br><strong>圖片預覽：</strong><br><img src='{$image_path}' style='max-width: 150px; border-radius: 5px; margin-top: 5px; border: 1px solid #ccc;'>";
        }

        // 發送 Email 通知邏輯
        $mail = new PHPMailer(true);
        try {
            // 1. SMTP 設定 (從環境變數讀取)
            $mail->isSMTP();
            $mail->Host       = 'smtp.office365.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['M365_USER']; // 從.env 讀取帳號
            $mail->Password   = $_ENV['M365_PASS']; // 從.env 讀取密碼
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // 寄件人與收件人
            $mail->setFrom($_ENV['M365_USER'], '包裹管理系統');
            
            // 組合出m365信箱 (學號 + 網域)
            $student_email = $student_id . '@m365.fju.edu.tw';
            $mail->addAddress($student_email, $student_name); // 收件人: 信箱, 姓名

            // 信件內容
            $mail->isHTML(true);
            $mail->Subject = '【包裹通知】您有一件包裹已抵達';
            
            // 郵件內文 (HTML)
            $mail_body = "
                <p><strong>{$student_name}</strong> 同學 您好：</p>
                <p>您有一件包裹已經抵達管理室，詳細資訊如下：</p>
                <ul>
                    <li><strong>包裹編號：</strong> {$new_id}</li>
                    <li><strong>抵達時間：</strong> {$arrive_time}</li>
                    <li><strong>收件學號：</strong> {$student_id}</li>
                </ul>
                <p>請攜帶學生證或相關證件至管理室領取。</p>
                <br>";

            $mail->Body = $mail_body;

            // 正式寄出
            $mail->send();
            $success_msg .= "<br>Email 通知已發送至：{$student_email}";

        } catch (Exception $e) {
            // 寄信失敗不影響包裹入庫，只顯示警告
            $success_msg .= "<br><span class='text-danger'>Email 發送失敗: {$mail->ErrorInfo}</span>";
        }

        $message = $success_msg;

    } else {
        $message = "error: 包裹登記失敗：" . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

// 右側功能 簽收包裹 
if (isset($_POST['receive_package'])) {
    $package_id = (int)($_POST['package_id'] ?? 0);
    $sql_check = "SELECT state, student_name FROM package WHERE package_id = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    if ($stmt_check === false) { die("資料庫錯誤: " . mysqli_error($conn)); }

    mysqli_stmt_bind_param($stmt_check, "i", $package_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $package = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);

    if ($package) {
        if ($package['state'] === 'N') {
            $sql_update = "UPDATE package SET receive_time = NOW(), state = 'Y' WHERE package_id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "i", $package_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $message = "success: 包裹 ID【{$package_id}】簽收成功！";
            } else {
                $message = "error: 更新失敗：" . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $message = "warning: 該包裹顯示為「已領取」狀態。";
        }
    } else {
        $message = "error: 找不到包裹 ID【{$package_id}】。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>包裹管理</title>
</head>
<body>

<div class="container mt-4">
    <h2>包裹管理作業</h2>
    <p><a href="admin/dashboard.php" class="text-decoration-none">← 返回管理儀表板</a></p>

    <?php if ($message): ?>
        <div class="alert <?= (strpos($message, 'success') !== false) ? 'alert-success' : (strpos($message, 'warning') !== false ? 'alert-warning' : 'alert-danger'); ?> alert-dismissible fade show" role="alert">
            <?= str_replace(['success: ', 'error: ', 'warning: '], '', $message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">1. 包裹抵達登記 (入庫)</div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">學號 (Student ID):</label>
                            <input type="number" name="student_id" class="form-control" required placeholder="將自動寄信至 學號@m365.fju.edu.tw">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">學生姓名:</label>
                            <input type="text" name="student_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">抵達時間:</label>
                            <input type="datetime-local" name="arrive_time" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">包裹圖片 (選填):</label>
                            <input type="file" name="fileToUpload" id="imgInput" class="form-control" accept="image/*">
                            <div class="mt-2 text-center">
                                <img id="previewImage" src="#" alt="圖片預覽" 
                                     style="display: none; max-width: 100%; max-height: 200px; border: 1px dashed #ccc; padding: 5px;">
                            </div>
                        </div>
                        <button type="submit" name="add_package" class="btn btn-primary w-100">新增包裹記錄 & 發送通知</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white fw-bold">2. 包裹簽收登記 (出庫)</div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">包裹 ID:</label>
                            <input type="number" name="package_id" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" name="receive_package" class="btn btn-success w-100 btn-lg">確認簽收</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('imgInput').onchange = function (evt) {
        var tgt = evt.target || window.event.srcElement, files = tgt.files;
        if (FileReader && files && files.length) {
            var fr = new FileReader();
            fr.onload = function () {
                var img = document.getElementById('previewImage');
                img.src = fr.result;
                img.style.display = 'block';
            }
            fr.readAsDataURL(files[0]);
        }
    }
</script>
</body>
</html>