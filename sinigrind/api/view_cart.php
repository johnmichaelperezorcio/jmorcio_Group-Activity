<?php
session_start();
require_once "../db.php";

header('Content-Type: application/json');

$user_id = intval($_GET['user_id'] ?? 0);

$cart = runQuery(
    "SELECT c.id, c.product_id, p.name, p.price, c.qty, (p.price * c.qty) AS subtotal
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?",
    ["i", [$user_id]]
);

echo json_encode($cart);
?>