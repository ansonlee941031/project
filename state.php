<?php
session_start();
require 'config/db.php';

// 給 header.php 用的變數
$title = "包裹 / 公物狀態總覽";


// 撈包裹資料
$packageSql    = "SELECT * FROM package ORDER BY package_id ASC";
$packageResult = mysqli_query($conn, $packageSql);

// 撈公物資料
$publicSql    = "SELECT * FROM public ORDER BY public_id ASC";
$publicResult = mysqli_query($conn, $publicSql);

// 下面會輸出 HTML，所以先 include header
include 'includes/header.php';
?>

<div class="container mt-4 mb-5">

  <!-- 包裹狀態表格 -->
  <h2 class="mb-3">包裹狀態</h2>
  <div class="table-responsive mb-5">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>包裹編號</th>
          <th>學生姓名</th>
          <th>學號</th>
          <th>到達時間</th>
          <th>領取時間</th>
          <th>狀態</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($packageResult && mysqli_num_rows($packageResult) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($packageResult)): ?>
            <?php
              // 轉換狀態 N / Y → 中文
              $stateText  = ($row['state'] === 'Y') ? '已領取' : '未領取';
              $stateBadge = ($row['state'] === 'Y') ? 'bg-success' : 'bg-warning text-dark';
            ?>
            <tr>
              <td><?= htmlspecialchars($row['package_id']) ?></td>
              <td><?= htmlspecialchars($row['student_name']) ?></td>
              <td><?= htmlspecialchars($row['student_id']) ?></td>
              <td><?= htmlspecialchars($row['arrive_time']) ?></td>
              <td><?= $row['receive_time'] ? htmlspecialchars($row['receive_time']) : '-' ?></td>
              <td>
                <span class="badge <?= $stateBadge ?>"><?= $stateText ?></span>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">目前沒有包裹資料</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- 公物狀態表格 -->
  <h2 class="mb-3">公物狀態</h2>
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>公物編號</th>
          <th>公物名稱</th>
          <th>狀態</th>
          <th>借出時間</th>
          <th>預計歸還時間</th>
          <th>借用者學號</th>
          <th>借用者姓名</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($publicResult && mysqli_num_rows($publicResult) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($publicResult)): ?>
            <?php
              // 假設 state = 'Y' 代表「在庫可借」，'N' 代表「已借出」
              $stateText  = ($row['state'] === 'Y') ? '在庫可借' : '已借出';
              $stateBadge = ($row['state'] === 'Y') ? 'bg-success' : 'bg-danger';
            ?>
            <tr>
              <td><?= htmlspecialchars($row['public_id']) ?></td>
              <td><?= htmlspecialchars($row['public_name']) ?></td>
              <td><span class="badge <?= $stateBadge ?>"><?= $stateText ?></span></td>
              <td><?= $row['borrow_time'] ? htmlspecialchars($row['borrow_time']) : '-' ?></td>
              <td><?= $row['expected_return_time'] ? htmlspecialchars($row['expected_return_time']) : '-' ?></td>
              <td><?= $row['borrower_id'] ? htmlspecialchars($row['borrower_id']) : '-' ?></td>
              <td><?= $row['borrower_name'] ? htmlspecialchars($row['borrower_name']) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">目前沒有公物資料</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Bootstrap JS（讓 navbar 的漢堡選單能動） -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>

