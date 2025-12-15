<?php
session_start();
require_once '../../db.php';

$product_id = intval($_POST['product_id'] ?? 0);
$price      = floatval($_POST['price'] ?? 0);
$stock      = intval($_POST['stock'] ?? 0);
$desc       = trim($_POST['description'] ?? '');
$photo      = null;

// Handle photo upload if provided
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $originalName = basename($_FILES['photo']['name']);
    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $originalName);
    $photo = time() . "_" . $safeName;

    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . $photo;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        runQuery("UPDATE products SET price=?, stock=?, description=?, photo=? WHERE id=?",
                 ["dissi", [$price, $stock, $desc, $photo, $product_id]]);
    }
} else {
    runQuery("UPDATE products SET price=?, stock=?, description=? WHERE id=?",
             ["disi", [$price, $stock, $desc, $product_id]]);
}

echo json_encode([
    "success" => true,
    "id" => $product_id,
    "price" => $price,
    "stock" => $stock,
    "description" => $desc,
    "photo" => $photo
]);