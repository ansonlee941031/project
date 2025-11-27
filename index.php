<?php 
session_start(); 

?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    
    <link rel="stylesheet" href="includes/custom.css">
</head>
<body>

<script>
    function selectRole(role) {
        document.getElementById('role').value = role;
        document.getElementById('tab-admin').classList.toggle('active', role === 'admin');
        document.getElementById('tab-resident').classList.toggle('active', role === 'resident');
    }
    window.onload = function () { selectRole('resident'); };
</script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>