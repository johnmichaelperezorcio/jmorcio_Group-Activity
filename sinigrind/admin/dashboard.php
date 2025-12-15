<?php
session_start();
require_once '../db.php';  // use db.php with runQuery()

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($action === 'add') {
        $name  = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $desc  = trim($_POST['description']);

        // Handle photo upload
        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $photo = time() . "_" . basename($_FILES['photo']['name']);
            $targetFile = $targetDir . $photo;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
        }

        runQuery("INSERT INTO products (name, price, stock, description, photo) VALUES (?, ?, ?, ?, ?)", 
                 ["sdiss", [$name, $price, $stock, $desc, $photo]]);
        $message = "✅ Product '{$name}' added successfully!";
    }
    elseif ($action === 'update' && $product_id > 0) {
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $desc  = trim($_POST['description']);

        if (!empty($_FILES['photo']['name'])) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $photo = time() . "_" . basename($_FILES['photo']['name']);
            $targetFile = $targetDir . $photo;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);

            runQuery("UPDATE products SET price=?, stock=?, description=?, photo=? WHERE id=?", 
                     ["dissi", [$price, $stock, $desc, $photo, $product_id]]);
        } else {
            runQuery("UPDATE products SET price=?, stock=?, description=? WHERE id=?", 
                     ["disi", [$price, $stock, $desc, $product_id]]);
        }
        $message = "✏️ Product #{$product_id} updated successfully!";
    }
    elseif ($action === 'delete' && $product_id > 0) {
        runQuery("DELETE FROM products WHERE id=?", ["i", [$product_id]]);
        $message = "🗑️ Product #{$product_id} deleted successfully!";
    }
}

// Fetch products
$products = runQuery("SELECT id, name, price, stock, description, photo FROM products ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            padding-top: 80px;
        }
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #212529;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar bg-dark text-white">
  <h4 class="text-center py-3"><b>M E N U</b></h4>
  <ul class="nav flex-column ps-4"> 
    <li class="nav-item">
      <a class="nav-link text-white" href="#" onclick="showView('dashboard', this)">
        <i class="bx bxs-home-smile active"></i>  Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white" href="#" onclick="showView('customer', this)">
        <i class="bx bxs-user-detail"></i>  Manage Customer
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white" href="#" onclick="showView('products', this)">
        <i class="bx bxs-shopping-bags"></i>  Manage Products
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white" href="#" onclick="showView('orders', this)">
        <i class="bx bxs-food-menu"></i>  Manage Orders
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-white" href="#" onclick="showView('sales-summary', this)">
        <i class="bx bx-bar-chart"></i>  Sales Summary
      </a>
    </li>
  </ul>
</div>

<!-- Topbar -->
<div class="topbar">
    <div>
        <!-- Left side of topbar (optional logo or title) -->
        <span class="fw-bold">Admin Dashboard</span>
    </div>
    <div>
        <!-- Right side: Logged in text + Logout -->
        <span class="me-3">Logged in as the Manager</span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
</div>

<!-- Content -->
<div class="content">

  <!--Dashboard-->
  <div id="dashboard">
    <h2>Dashboard</h2>
  </div>

  <!--Customer-->
  <div id="customer" style="display:none">
    <h2>Customer Management</h2>
  </div>

  <!--Products-->
  <div id="products" style="display:none">
    <h2 id="product-management" class="mb-4">Product Management Dashboard</h2>
    <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- Add product form -->
        <div class="card mb-4">
            <div class="card-header">Add New Product</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                    </div>
                    <div class="col-md-3">
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12">
                        <textarea name="description" class="form-control" placeholder="Product Description"></textarea>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product list -->
<h3>Existing Products</h3>
<table class="table table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price (₱)</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <?php if (!empty($p['photo'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($p['photo']) ?>" 
                             alt="Product Photo" style="width:80px;height:auto;">
                    <?php else: ?>
                        <span class="text-muted">No photo</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['description']) ?></td>
                <td>₱<?= number_format($p['price'], 2) ?></td>
                <td><?= intval($p['stock']) ?></td>
                <td>
                    <!-- Update form -->
                    <form method="post" enctype="multipart/form-data" class="update-form d-inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">

                        <div class="mb-2">
                            <input type="number" step="0.01" name="price" 
                                   value="<?= $p['price'] ?>" class="form-control form-control-sm" 
                                   placeholder="Price">
                        </div>
                        <div class="mb-2">
                            <input type="number" name="stock" 
                                   value="<?= $p['stock'] ?>" class="form-control form-control-sm" 
                                   placeholder="Stock">
                        </div>
                        <div class="mb-2">
                            <input type="file" name="photo" accept="image/*" 
                                   class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <textarea name="description" class="form-control form-control-sm" 
                                      placeholder="Description"><?= htmlspecialchars($p['description']) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>

                    <!-- Delete form -->
                    <form method="post" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center">No products found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
    </div>

  <!--Orders-->
  <div id="orders" style="display:none">
    <h2>Orders</h2>
  </div>

  <!--Sales Summary-->
  <div id="sales-summary" style="display:none">
    <h2>Sales Summary</h2>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  // Sidebar Navigation
  function showView(view, el){
    // hide all sections
    ['dashboard','customer','products','orders','sales-summary'].forEach(v=>{
      const section = document.getElementById(v);
      if(section) section.style.display = (v===view)?'block':'none';
    });

    // remove active from all links
    document.querySelectorAll('.sidebar a').forEach(a=>a.classList.remove('active'));
    // add active to clicked link
    if(el) el.classList.add('active');
  }

  // set default view on page load
  document.addEventListener('DOMContentLoaded', ()=>{
    showView('dashboard', document.querySelector('.sidebar a[href="#dashboard"]'));
  });

$(document).on("submit", ".update-form", function(e) {
  e.preventDefault(); // stop reload

  var form = this;
  var formData = new FormData(form);

  $.ajax({
    url: "api/product_update.php",   // your PHP update endpoint
    type: "POST",
    data: formData,
    processData: false, // required for FormData
    contentType: false, // required for FormData
    success: function(response) {
      var data = JSON.parse(response);
      if (data.success) {
        var row = $(form).closest("tr");
        row.find("td:nth-child(5)").text("₱" + parseFloat(data.price).toFixed(2));
        row.find("td:nth-child(6)").text(data.stock);
        row.find("td:nth-child(4)").text(data.description);
        if (data.photo) {
          row.find("td:nth-child(2)").html(
            `<img src="uploads/${data.photo}" alt="Product Photo" style="width:80px;height:auto;">`
          );
        }
        alert("Product updated successfully!");
      }
    },
    error: function() {
      alert("Update failed.");
    }
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</body>
</html>