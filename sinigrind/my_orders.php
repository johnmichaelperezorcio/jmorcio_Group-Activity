<?php
session_start();
require_once "db.php";

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    echo "<p>You must log in to view your orders.</p>";
    exit;
}

// Fetch orders for this user
$orders = runQuery("SELECT id, `total_amount` AS total_amount, status, created_at 
                    FROM orders 
                    WHERE user_id=? 
                    ORDER BY created_at DESC", ["i", [$user_id]]);

// Fetch items for each order
function getOrderItems($order_id) {
    return runQuery("SELECT oi.product_id, p.name, oi.qty, oi.price, oi.subtotal
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = ?", ["i", [$order_id]]);
}

// Map statuses to progress steps
function getProgressStep($status) {
    $steps = ["pending" => 1, "processing" => 2, "shipped" => 3, "completed" => 4];
    return $steps[strtolower($status)] ?? 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-step {
            width: 25%;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">My Orders</h2>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $o): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Order #<?= $o['id'] ?></strong> 
                    | Date: <?= $o['created_at'] ?> 
                    | Status: <span class="badge bg-info"><?= htmlspecialchars($o['status']) ?></span>
                </div>
                <div class="card-body">
                    <p>Total: ₱<?= number_format($o['total_amount'], 2) ?></p>

                    <!-- Progress bar -->
                    <?php $step = getProgressStep($o['status']); ?>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= ($step/4)*100 ?>%;" 
                             aria-valuenow="<?= $step ?>" aria-valuemin="0" aria-valuemax="4">
                            <?= ucfirst($o['status']) ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="progress-step">Pending</div>
                        <div class="progress-step">Processing</div>
                        <div class="progress-step">Shipped</div>
                        <div class="progress-step">Completed</div>
                    </div>

                    <!-- Order items -->
                    <ul class="mt-3">
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

                    <!-- Receipt button only if completed -->
                    <div class="mt-3">
                        <?php if (strtolower($o['status']) === 'completed'): ?>
                            <a href="receipt.php?order_id=<?= $o['id'] ?>" 
                            class="btn btn-secondary btn-sm">
                                View Receipt
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                                Receipt available after completion
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>
</div>
</body>
</html>