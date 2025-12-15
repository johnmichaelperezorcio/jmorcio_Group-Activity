<?php
require_once "../db.php";

/**
 * Add item to cart (insert or update)
 */
function addToCart($user_id, $product_id, $qty) {
    // Check if item already exists in cart
    $existing = runQuery(
        "SELECT id, qty FROM cart WHERE user_id=? AND product_id=?",
        ["ii", [$user_id, $product_id]]
    );

    if ($existing) {
        // Update quantity
        runQuery(
            "UPDATE cart SET qty = qty + ? WHERE id=?",
            ["ii", [$qty, $existing[0]['id']]]
        );
    } else {
        // Insert new cart row
        runQuery(
            "INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, ?)",
            ["iii", [$user_id, $product_id, $qty]]
        );
    }
}

/**
 * Get all cart items for a user
 */
function getCart($user_id) {
    return runQuery(
        "SELECT c.id, c.product_id, p.name, p.price, c.qty, (p.price * c.qty) AS subtotal
         FROM cart c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = ?",
        ["i", [$user_id]]
    );
}

/**
 * Update cart item quantity
 */
function updateCart($cart_id, $qty) {
    return runQuery(
        "UPDATE cart SET qty=? WHERE id=?",
        ["ii", [$qty, $cart_id]]
    );
}

/**
 * Remove item from cart
 */
function removeFromCart($cart_id) {
    return runQuery(
        "DELETE FROM cart WHERE id=?",
        ["i", [$cart_id]]
    );
}
?>