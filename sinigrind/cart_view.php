<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-4">
  <h2 class="mb-4">Your Cart</h2>
  <table class="table table-bordered" id="cartTable">
    <thead class="table-dark">
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th></th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <button class="btn btn-success w-100" onclick="placeOrder()">Checkout</button>
</div>

<script>
var USER_ID = <?= $user_id ?>;
loadCart();

function loadCart() {
  $.get("api/view_cart.php", { user_id: USER_ID }, function(cart) {
    let tbody = $("#cartTable tbody");
    tbody.html("");
    if (!cart || cart.length === 0) {
      tbody.append('<tr><td colspan="5" class="text-center">No items in Cart</td></tr>');
      return;
    }
    cart.forEach(item => {
      tbody.append(`
        <tr>
          <td>${item.name}</td>
          <td><input type="number" value="${item.qty}" onchange="updateQty(${item.id}, this.value)" class="form-control"></td>
          <td>₱${parseFloat(item.price).toFixed(2)}</td>
          <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
          <td><button class="btn btn-danger btn-sm" onclick="removeItem(${item.id})">Remove</button></td>
        </tr>
      `);
    });
  }, "json");
}

function updateQty(cart_id, qty) {
  $.post("api/update_cart.php", { cart_id, qty }, loadCart);
}

function removeItem(cart_id) {
  $.post("api/remove_from_cart.php", { cart_id }, function() {
    alert("Item removed!");
    loadCart();
  });
}

function placeOrder() {
  $.post("api/place_order.php", { user_id: USER_ID }, function(data) {
    if (data.status === "success") {
      alert("Order placed! ID: " + data.order_id);
      loadCart();
    } else {
      alert("Error: " + data.message);
    }
  }, "json");
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>