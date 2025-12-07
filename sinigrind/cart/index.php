<?php
session_start();
require_once "db.php";   // your db.php with runQuery()

error_log("Index loaded for user_id=" . $_SESSION['user_id']);

// If user not logged in, show login button and stop
if (!isset($_SESSION['user_id'])) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Simple Cart System - Login Required</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh;">
        <div class="text-center">
            <h2 class="mb-4">You must log in to access the shop</h2>
            <a href="login.php" class="btn btn-primary btn-lg">Go to Login</a>
        </div>
    </body>
    </html>';
    exit;
}

// Fetch products dynamically
$products = runQuery("SELECT * FROM products ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Cart System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Simple Cart</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto"> <!-- push to right -->
        <li class="nav-item">
          <a class="nav-link" href="my_orders.php">My Orders</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-light ms-2" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
    <h2 class="mb-4">Product List</h2>

    <div class="row" id="productList">
        <?php foreach ($products as $p): ?>
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                    <p class="card-text">₱<?= number_format($p['price'],2) ?></p>
                    <input type="number" class="form-control mb-2" value="1" id="qty<?= $p['id'] ?>">
                    <button class="btn btn-primary w-100"
                        onclick="addToCart(<?= $p['id'] ?>, $('#qty<?= $p['id'] ?>').val())">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <h2 class="mb-3">Your Cart</h2>

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
        <tbody>
            <!-- items will load here via loadCart() -->
        </tbody>
    </table>

    <button class="btn btn-success w-100" onclick="placeOrder()">Checkout</button>
</div>

<script>
    var USER_ID = <?php echo (int)$_SESSION['user_id']; ?>;
    loadCart();

    function loadCart() {
        $.ajax({
            url: "api/view_cart.php",
            method: "GET",
            data: { user_id: USER_ID },
            dataType: "json",
            success: function(cart) {
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
                            <td>
                                <input type="number" class="form-control"
                                value="${item.qty}"
                                onchange="updateQty(${item.id}, this.value)">
                            </td>
                            <td>₱${parseFloat(item.price).toFixed(2)}</td>
                            <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                            <td>
                                <button class="btn btn-danger" onclick="removeItem(${item.id})">X</button>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    function addToCart(product_id, qty) {
        $.ajax({
            url: "api/add_to_cart.php",
            method: "POST",
            data: { user_id: USER_ID, product_id: product_id, qty: qty },
            success: function() {
                alert("Item added to cart!");
                loadCart();
            }
        });
    }

    function updateQty(cart_id, qty) {
        $.ajax({
            url: "api/update_cart.php",
            method: "POST",
            data: { cart_id: cart_id, qty: qty },
            success: function() {
                loadCart();
            }
        });
    }

    function removeItem(cart_id) {
        $.ajax({
            url: "api/remove_from_cart.php",
            method: "POST",
            data: { cart_id: cart_id },
            success: function() {
                alert("Item removed!");
                loadCart();
            }
        });
    }

    function placeOrder() {
        let cartRows = $("#cartTable tbody tr").length;
        if (cartRows === 0 || $("#cartTable tbody tr").first().find("td").text().includes("No items")) {
            alert("Your cart is empty. Please add items before checking out.");
            return;
        }
        $.ajax({
            url: "api/place_order.php",
            method: "POST",
            data: { user_id: USER_ID },
            dataType: "json",
            success: function(data) {
                if (data.status === "success") {
                    alert("Order placed! Order ID: " + data.order_id);
                    loadCart();
                } else {
                    alert("Error: " + data.message);
                }
            }
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>