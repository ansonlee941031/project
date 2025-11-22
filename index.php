<?php 
session_start(); 
require 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title>登入</title>
        <style>
            body { font-family: "Microsoft JhengHei", sans-serif; background:#f5f5f5; }
            .login-container { width:360px; margin:80px auto; padding:20px; background:#fff; }
            .role-tabs { display:flex; border-bottom:1px solid #ccc; margin-bottom:15px; }
            .role-tab { flex:1; text-align:center; padding:8px 0; cursor:pointer; }
            .role-tab.active { font-weight:bold; border-bottom:3px solid #007bff; }
            label { display:block; margin-top:10px; }
            input[type=text], input[type=password] { width:100%; padding:6px; box-sizing:border-box; }
            button { width:100%; margin-top:15px; padding:8px; border:none; background:#007bff; color:#fff; }
            .error { color:#c00; margin-top:10px; text-align:center; }
        </style>
        <script>
            function selectRole(role) {
                document.getElementById('role').value = role;
                document.getElementById('tab-admin').classList.toggle('active', role === 'admin');
                document.getElementById('tab-resident').classList.toggle('active', role === 'resident');
            }
            window.onload = function () { selectRole('resident'); };
        </script>
    </head>
    <body>
    <div class="login-container">
        <div class="role-tabs">
            <div id="tab-admin" class="role-tab" onclick="selectRole('admin')">Admin</div>
            <div id="tab-resident" class="role-tab" onclick="selectRole('resident')">Resident</div>
        </div>

        <form action="login_process.php" method="post">
            <input type="hidden" name="role" id="role" value="resident">

            <label>帳號：</label>
            <input type="text" name="username" required>

            <label>密碼：</label>
            <input type="password" name="password" required>

            <button type="submit">登入</button>

            <?php if (isset($_GET['error'])): ?>
                <div class="error">帳號 / 密碼 / 身分 錯誤</div>
            <?php endif; ?>
        </form>
    </div>
    </body>
</html>
 