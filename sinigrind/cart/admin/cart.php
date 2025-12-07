<?php 
// cart.php 
require_once '../db.php'; 
require_once "admin_functions.php";

/** 
* CART structure in session: 
* $_SESSION['cart'] = [ 
*   product_id => qty, 
*   ... 
* ]; 
*/ 
function cart_get() { 
    return isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? 
$_SESSION['cart'] : []; 
} 
function cart_set($cart) { 
    $_SESSION['cart'] = $cart; 
} 
/** 
* add_to_cart($product_id, $qty) 
*/ 
function add_to_cart($product_id, $qty = 1) { 
    $product_id = (int)$product_id; 
    $qty = max(1, (int)$qty); 
    $cart = cart_get(); 
    if (isset($cart[$product_id])) { 
    $cart[$product_id] += $qty;
     } else { 
        $cart[$product_id] = $qty; 
    } 
    cart_set($cart); 
} 
 
/** 
 * update_cart_item($product_id, $qty) 
 */ 
function update_cart_item($product_id, $qty) { 
    $product_id = (int)$product_id; 
    $qty = (int)$qty; 
    $cart = cart_get(); 
    if ($qty <= 0) { 
        unset($cart[$product_id]); 
    } else { 
        $cart[$product_id] = $qty; 
    } 
    cart_set($cart); 
} 
 
/** 
 * remove_from_cart($product_id) 
 */ 
function remove_from_cart($product_id) { 
    $cart = cart_get(); 
    $product_id = (int)$product_id; 
    if (isset($cart[$product_id])) { 
        unset($cart[$product_id]); 
        cart_set($cart); 
    } 
} 
 
/** 
 * empty_cart() 
 */ 
function empty_cart() { 
    unset($_SESSION['cart']); 
} 
 
// cart_items_with_details() - fetch product details for items in cart
// returns array of items: each item => ['product' => row, 'qty' => int, 'subtotal' => float]
function cart_items_with_details() { 
    $cart = cart_get(); 
    if (empty($cart)) return []; 
 
    $ids = array_keys($cart); 
    // build placeholders: "?, ?, ?" - but our utility expects '?' placeholders; we'll use IN via string because mysqli and bind with IN is complicated. 
    // Simpler: fetch per id (small shop) or create a safe comma list 
    $safe_ids = array_map('intval', $ids); 
    $id_list = implode(',', $safe_ids); 
    $sql = "SELECT id, name, price, stock FROM products WHERE id IN 
($id_list)"; 
    $rows = db_query_all($sql, []); // no params because sanitized ints 
    $byId = []; 
    foreach ($rows as $r) $byId[$r['id']] = $r; 
 
    $items = []; 
    foreach ($cart as $pid => $qty) { 
        if (!isset($byId[$pid])) continue; // product removed from DB 
        $price = (float)$byId[$pid]['price']; 
        $items[] = [ 
            'product' => $byId[$pid], 
            'qty' => (int)$qty, 
            'subtotal' => $price * (int)$qty 
        ]; 
    } 
    return $items; 
} 
 
/** 
 * cart_total() 
 */ 
function cart_total() { 
    $items = cart_items_with_details(); 
    $total = 0.0; 
    foreach ($items as $it) $total += $it['subtotal']; 
    return $total; 
}

?>