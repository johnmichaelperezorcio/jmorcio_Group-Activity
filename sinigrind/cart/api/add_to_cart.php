<?php
session_start();
require_once "../db.php";

$user_id = intval($_POST['user_id']);
$product_id = intval($_POST['product_id']);
$qty = intval($_POST['qty']);

if ($user_id > 0 && $product_id > 0 && $qty > 0) {
    // Check if item already in cart
    $existing = runQuery(
        "SELECT id, qty FROM cart WHERE user_id=? AND product_id=?",
        ["ii", [$user_id, $product_id]]
    );

    if ($existing) {
        // Update qty
        runQuery(
            "UPDATE cart SET qty = qty + ? WHERE id=?",
            ["ii", [$qty, $existing[0]['id']]]
        );
    } else {
        // Insert new row
        runQuery(
            "INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, ?)",
            ["iii", [$user_id, $product_id, $qty]]
        );
    }

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>