<?php 
session_start();
require_once '../db.php';  // use db.php with runQuery()

// Optional: check if user is admin
// if ($_SESSION['role'] !== 'admin') { die("Access denied"); }

$message = "";

// Handle form submissions directly here
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($action === 'add') {
        $name  = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        runQuery("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)", 
                 ["sdi", [$name, $price, $stock]]);
        $message = "✅ Product '{$name}' added successfully!";
    }
    elseif ($action === 'update' && $product_id > 0) {
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        runQuery("UPDATE products SET price=?, stock=? WHERE id=?", 
                 ["dii", [$price, $stock, $product_id]]);
        $message = "✏️ Product #{$product_id} updated successfully!";
    }
    elseif ($action === 'delete' && $product_id > 0) {
        runQuery("DELETE FROM products WHERE id=?", ["i", [$product_id]]);
        $message = "🗑️ Product #{$product_id} deleted successfully!";
    }
}

// Fetch products from DB after any changes
$products = runQuery("SELECT id, name, price, stock FROM products ORDER BY id ASC");
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Product Management Dashboard</h2>

    <!-- Show alert if message exists -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add new product form -->
    <div class="card mb-4">
        <div class="card-header">Add New Product</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <input type="hidden" name="action" value="add">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing products -->
    <h3>Existing Products</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
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
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td>₱<?= number_format($p['price'], 2) ?></td>
                    <td><?= intval($p['stock']) ?></td>
                    <td>
                        <!-- Update form -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="number" step="0.01" name="price" value="<?= $p['price'] ?>" style="width:80px">
                            <input type="number" name="stock" value="<?= $p['stock'] ?>" style="width:60px">
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
            <tr><td colspan="5" class="text-center">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>