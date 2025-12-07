<?php
session_start();
require_once '../db.php';

$action = $_POST['action'] ?? '';
$product_id = intval($_POST['product_id'] ?? 0);

if ($action === 'add') {
    runQuery("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)", 
             ["sdi", [$_POST['name'], $_POST['price'], $_POST['stock']]]);
    echo "Product added!";
}
elseif ($action === 'update' && $product_id > 0) {
    runQuery("UPDATE products SET price=?, stock=? WHERE id=?", 
             ["dii", [$_POST['price'], $_POST['stock'], $product_id]]);
    echo "Product updated!";
}
elseif ($action === 'delete' && $product_id > 0) {
    runQuery("DELETE FROM products WHERE id=?", ["i", [$product_id]]);
    echo "Product deleted!";
}
else {
    echo "Invalid action.";
}
?>