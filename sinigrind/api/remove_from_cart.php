<?php
session_start();
require_once "../db.php";

$cart_id = intval($_POST['cart_id'] ?? 0);

if ($cart_id > 0) {
    runQuery(
        "DELETE FROM cart WHERE id=?",
        ["i", [$cart_id]]
    );
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid cart_id"]);
}
?>