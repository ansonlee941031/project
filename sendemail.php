<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 載入 dotenv 套件
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();



$mail = new PHPMailer(true);

try {
    // SMTP 設定
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['M365_USER'];
    $mail->Password   = $_ENV['M365_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 收件人
    $mail->setFrom($_ENV['M365_USER'], 'Your Name');
    $mail->addAddress('413401479@m365.fju.edu.tw');

    // 郵件內容
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(false);
    $mail->Subject = '測試';
    $mail->Body    = '這是一封測試郵件';

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
?>