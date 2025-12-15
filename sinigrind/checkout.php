<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    die("Not logged in");
}

// Get cart items
$cart_items = runQuery(
    "SELECT c.id, p.id AS product_id, p.price, c.qty
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?",
    ["i", [$user_id]]
);

if (empty($cart_items)) {
    die("Cart is empty");
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['qty'];
}

// Create order
$order_id = runQuery(
    "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')",
    ["id", [$user_id, $total]]
);

// Insert order items
foreach ($cart_items as $item) {
    runQuery(
        "INSERT INTO order_items (order_id, product_id, qty, price, subtotal)
         VALUES (?, ?, ?, ?, ?)",
        ["iiidd", [$order_id, $item['product_id'], $item['qty'], $item['price'], $item['price'] * $item['qty']]]
    );
}

// Clear cart
runQuery("DELETE FROM cart WHERE user_id = ?", ["i", [$user_id]]);

echo "Order placed successfully!";
?>