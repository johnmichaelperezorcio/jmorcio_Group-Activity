<?php
session_start();
require_once '../db.php';

// Protect admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Yearly summary
$yearly = runQuery("SELECT YEAR(created_at) AS year,
                           SUM(total_amount) AS total_sales,
                           COUNT(*) AS order_count
                    FROM orders
                    WHERE status='completed'
                    GROUP BY year
                    ORDER BY year DESC");

// Monthly summary
$monthly = runQuery("SELECT DATE_FORMAT(created_at, '%Y-%m') AS period,
                            SUM(total_amount) AS total_sales,
                            COUNT(*) AS order_count
                     FROM orders
                     WHERE status='completed'
                     GROUP BY period
                     ORDER BY period DESC");

// Weekly summary
$weekly = runQuery("SELECT YEARWEEK(created_at, 1) AS period,
                           SUM(total_amount) AS total_sales,
                           COUNT(*) AS order_count
                    FROM orders
                    WHERE status='completed'
                    GROUP BY period
                    ORDER BY period DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Sales Summary</span>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>

<div class="container py-4">

  <!-- Yearly chart -->
  <h2>Yearly Sales Overview</h2>
  <canvas id="yearlyChart" height="100"></canvas>

  <script>
    const yearlyData = {
      labels: <?= json_encode(array_column($yearly, 'year')) ?>,
      datasets: [{
        label: 'Total Sales (₱)',
        data: <?= json_encode(array_column($yearly, 'total_sales')) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.7)'
      }]
    };
    new Chart(document.getElementById('yearlyChart'), {
      type: 'bar',
      data: yearlyData
    });
  </script>

  <!-- Monthly table -->
  <h2 class="mt-5">Monthly Sales</h2>
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr><th>Month</th><th>Total Sales (₱)</th><th>Orders</th></tr>
    </thead>
    <tbody>
      <?php foreach ($monthly as $m): ?>
        <tr>
          <td><?= $m['period'] ?></td>
          <td>₱<?= number_format($m['total_sales'],2) ?></td>
          <td><?= $m['order_count'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Weekly table collapsible -->
  <h2 class="mt-5">Weekly Sales</h2>
  <button class="btn btn-secondary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#weeklyTable">
    Toggle Weekly Breakdown
  </button>
  <div class="collapse" id="weeklyTable">
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr><th>Week</th><th>Total Sales (₱)</th><th>Orders</th></tr>
      </thead>
      <tbody>
        <?php foreach ($weekly as $w): ?>
          <tr>
            <td><?= $w['period'] ?></td>
            <td>₱<?= number_format($w['total_sales'],2) ?></td>
            <td><?= $w['order_count'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>