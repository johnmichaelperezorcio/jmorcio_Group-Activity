<?php
session_start();
require_once "db.php";

$order_id = intval($_GET['order_id'] ?? 0);
if ($order_id <= 0) { die("Invalid order."); }

// Fetch order
$order = runQuery("SELECT o.id, o.total_amount, o.status, o.created_at, u.username, u.email
                   FROM orders o
                   JOIN users u ON o.user_id = u.id
                   WHERE o.id=?", ["i", [$order_id]]);
if (!$order) { die("Order not found."); }
$order = $order[0];

// Fetch items
$items = runQuery("SELECT oi.product_id, p.name, oi.qty, oi.price, oi.subtotal
                   FROM order_items oi
                   JOIN products p ON oi.product_id = p.id
                   WHERE oi.order_id=?", ["i", [$order_id]]);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Receipt #<?= $order['id'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h2>Receipt for Order #<?= $order['id'] ?></h2>
  <p>User: <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
  <p>Date: <?= $order['created_at'] ?></p>
  <p>Status: <?= ucfirst($order['status']) ?></p>

  <table class="table table-bordered mt-3">
    <thead>
      <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= $item['qty'] ?></td>
          <td>₱<?= number_format($item['price'], 2) ?></td>
          <td>₱<?= number_format($item['subtotal'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h4>Total: ₱<?= number_format($order['total_amount'], 2) ?></h4>
  <p class="mt-3">Thank you for shopping with us!</p>

  <button class="btn btn-primary" onclick="window.print()">🖨️ Print Receipt</button>
</body>
</html>