<?php
session_start();
require_once '../../db.php';

$name  = trim($_POST['name'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$desc  = trim($_POST['description'] ?? '');
$photo = null;

// Handle photo upload
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $originalName = basename($_FILES['photo']['name']);
    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $originalName);
    $photo = time() . "_" . $safeName;

    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . $photo;
    move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
}

runQuery("INSERT INTO products (name, price, stock, description, photo) VALUES (?, ?, ?, ?, ?)",
         ["sdiss", [$name, $price, $stock, $desc, $photo]]);

$newId = runQuery("SELECT LAST_INSERT_ID() AS id")[0]['id'];

echo json_encode([
    "success" => true,
    "id" => $newId,
    "name" => $name,
    "price" => $price,
    "stock" => $stock,
    "description" => $desc,
    "photo" => $photo
]);
exit;