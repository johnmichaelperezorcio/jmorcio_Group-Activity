<?php
require_once "../db.php";

/**
 * Add a new product
 */
function addProduct($name, $price, $stock) {
    return runQuery(
        "INSERT INTO products (name, price, stock) VALUES (?, ?, ?)",
        ["sdi", [$name, $price, $stock]]
    );
}

/**
 * Update product stock
 */
function updateStock($product_id, $new_stock) {
    return runQuery(
        "UPDATE products SET stock=? WHERE id=?",
        ["ii", [$new_stock, $product_id]]
    );
}

/**
 * Get all orders
 */
function getAllOrders() {
    return runQuery("SELECT * FROM orders ORDER BY created DESC");
}

/**
 * Update order status
 */
function updateOrderStatus($order_id, $status) {
    return runQuery(
        "UPDATE orders SET statu=? WHERE id=?",
        ["si", [$status, $order_id]]
    );
}
?>