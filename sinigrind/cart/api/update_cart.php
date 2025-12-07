<?php
session_start();
require_once "../db.php";

$cart_id = intval($_POST['cart_id'] ?? 0);
$qty     = intval($_POST['qty'] ?? 0);

if ($cart_id > 0 && $qty > 0) {
    runQuery(
        "UPDATE cart SET qty=? WHERE id=?",
        ["ii", [$qty, $cart_id]]
    );
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>