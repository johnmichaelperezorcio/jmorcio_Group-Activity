<?php
session_start();
require_once "../db.php";
require_once "cart_functions.php";

header('Content-Type: application/json');

$user_id = intval($_POST['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user"]);
    exit;
}

// Get cart items
$cart_items = getCart($user_id);

if (empty($cart_items)) {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    exit;
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
}

// Create order
$order_id = runQuery(
    "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())",
    ["id", [$user_id, $total]]
);

// Insert order items
foreach ($cart_items as $item) {
    runQuery(
        "INSERT INTO order_items (order_id, product_id, qty, price, subtotal)
         VALUES (?, ?, ?, ?, ?)",
        ["iiidd", [$order_id, $item['product_id'], $item['qty'], $item['price'], $item['subtotal']]]
    );
}

// Clear cart
runQuery("DELETE FROM cart WHERE user_id=?", ["i", [$user_id]]);

echo json_encode(["status" => "success", "order_id" => $order_id]);
?>