<?php
session_start();
require_once '../db.php';

// Optional: check if user is admin
// if ($_SESSION['role'] !== 'admin') { die("Access denied"); }

$message = "";

// Handle status updates inline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id'] ?? 0);
    $status   = trim($_POST['status'] ?? '');

    if ($order_id > 0 && !empty($status)) {
        runQuery("UPDATE orders SET status=? WHERE id=?", ["si", [$status, $order_id]]);
        $message = "✅ Order #{$order_id} status updated to '{$status}'!";
    }
}

// Fetch all orders
$orders = runQuery("SELECT o.id, u.username, o.total_amount, o.status, o.created_at 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    ORDER BY o.created_at DESC");

// Fetch order items for each order
function getOrderItems($order_id) {
    return runQuery("SELECT oi.product_id, p.name, oi.qty, oi.price, oi.subtotal
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = ?", ["i", [$order_id]]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Orders Management</h2>

    <!-- Show alert if message exists -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Created</th>
                <th>Items</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['username']) ?></td>
                    <td>₱<?= number_format($o['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($o['status']) ?></td>
                    <td><?= $o['created_at'] ?></td>
                    <td>
                        <ul class="mb-0">
                        <?php $items = getOrderItems($o['id']); ?>
                        <?php foreach ($items as $item): ?>
                            <li>
                                <?= htmlspecialchars($item['name']) ?> 
                                (x<?= intval($item['qty']) ?>) - 
                                ₱<?= number_format($item['price'], 2) ?> each, 
                                Subtotal: ₱<?= number_format($item['subtotal'], 2) ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <!-- Update status form -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" class="form-select form-select-sm d-inline w-auto">
                                <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
                                <option value="processing" <?= $o['status']=='processing'?'selected':'' ?>>Processing</option>
                                <option value="shipped" <?= $o['status']=='shipped'?'selected':'' ?>>Shipped</option>
                                <option value="completed" <?= $o['status']=='completed'?'selected':'' ?>>Completed</option>
                                <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>