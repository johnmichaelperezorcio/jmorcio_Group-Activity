<?php
// api/cart_count.php
require_once "../db.php";   // adjust path if needed

header("Content-Type: application/json");

$user_id = $_GET['user_id'] ?? 0;
$user_id = (int)$user_id;

if ($user_id <= 0) {
    echo json_encode(0);
    exit;
}

// Count items in cart for this user
$result = runQuery("SELECT SUM(qty) AS count FROM cart WHERE user_id = ?", ["i", [$user_id]]);
$count = $result && $result[0]['count'] ? (int)$result[0]['count'] : 0;

echo json_encode($count);