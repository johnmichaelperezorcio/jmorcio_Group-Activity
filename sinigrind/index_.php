<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$products = runQuery("SELECT id, name, price, stock, description, photo FROM products ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Simple Cart System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Simple Cart</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="cart_view.php">
            My Cart <span id="cartBadge" class="badge bg-light text-dark">0</span>
          </a>
        </li>
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
  <div class="row">
    <?php foreach ($products as $p): ?>
      <div class="col-md-4">
        <div class="card shadow-sm mb-4 h-100">
          <?php if (!empty($p['photo'])): ?>
            <img src="uploads/<?= htmlspecialchars($p['photo']) ?>" class="card-img-top" alt="Product Photo" style="height:200px;object-fit:cover;">
          <?php else: ?>
            <img src="https://via.placeholder.com/200x200?text=No+Image" class="card-img-top" alt="No Photo">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
            <p class="card-text text-muted"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
            <p class="card-text fw-bold">₱<?= number_format($p['price'],2) ?></p>
            <div class="mt-auto">
              <input type="number" class="form-control mb-2" value="1" id="qty<?= $p['id'] ?>" min="1">
              <button class="btn btn-primary w-100"
                onclick="addToCart(<?= $p['id'] ?>, $('#qty<?= $p['id'] ?>').val())">
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
var USER_ID = <?= (int)$_SESSION['user_id'] ?>;

function addToCart(product_id, qty) {
  $.post("api/add_to_cart.php", { user_id: USER_ID, product_id: product_id, qty: qty }, function() {
    alert("Item added to cart!");
    updateCartBadge();
  });
}

function updateCartBadge() {
  $.get("api/cart_count.php", { user_id: USER_ID }, function(count) {
    $("#cartBadge").text(count);
  });
}

// Load badge count on page load
updateCartBadge();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>