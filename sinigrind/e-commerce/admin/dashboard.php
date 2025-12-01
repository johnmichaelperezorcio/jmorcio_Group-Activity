<?php
include '../components/conn.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiniGrind - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="main-container">
        <?php include '../components/admin_header.php'; ?>

        <section class="dashboard">
            <div class="heading">
                <img src="../assets/imgs/separator.png">
            </div>

            <div class="box-container">
                <div class="box">
                    <h3>Welcome!</h3>

                    <p><?= $fetch_profile['name']; ?></p>
                    <a href="update.php" class="btn">Update Profile</a>
                </div>
                <div class="box">
                    <?php
                    $select_message = $conn->prepare("SELECT * FROM `message`");
                    $select_message->execute();
                    $number_of_msg = $select_message->rowCount();
                    ?>
                    <h3><?= $number_of_msg; ?></h3>
                    <p>unread messages</p>
                    <a href="admin_message.php" class="btn">see message</a>
                </div>  

                <div class="box">
                    <?php
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE admin_id = ?");
                    $select_products->execute([$admin_id]);
                    $number_of_products = $select_products->rowCount();
                    ?>
                    <h3><?= $number_of_products; ?></h3>
                    <p>products added</p>
                    <a href="add_products.php" class="btn">add products</a>
                </div>

                <div class="box">
                    <?php
                    $select_active_products = $conn->prepare("SELECT * FROM `products` WHERE admin_id = ? AND status = ?");
                    $select_active_products->execute([$admin_id, 'active']);
                    $number_of_active_products = $select_active_products->rowCount();
                    ?>
                    <h3><?= $number_of_active_products; ?></h3>
                    <p>total active products</p>
                    <a href="view_products.php" class="btn">active products</a>
                </div>

                <div class="box">
                    <?php
                    $select_inactive_products = $conn->prepare("SELECT * FROM `products` WHERE admin_id = ? AND status = ?");
                    $select_inactive_products->execute([$admin_id, 'inactive']);
                    $number_of_inactive_products = $select_inactive_products->rowCount();
                    ?>
                    <h3><?= $number_of_inactive_products; ?></h3>
                    <p>total inactive products</p>
                    <a href="view_products.php" class="btn">inactive products</a>
                </div>

                <div class="box">
                    <?php
                    $select_users = $conn->prepare("SELECT * FROM `users`");
                    $select_users->execute();
                    $number_of_users = $select_users->rowCount();
                    ?>
                    <h3><?= $number_of_users; ?></h3>
                    <p>users account</p>
                    <a href="user_accounts.php" class="btn">see users</a>
                </div>

                <div class="box">
                    <?php
                    $select_admin = $conn->prepare("SELECT * FROM `admin`");
                    $select_admin->execute();
                    $number_of_admin = $select_admin->rowCount();
                    ?>
                    <h3><?= $number_of_admin; ?></h3>
                    <p>admin account</p>
                    <a href="view_admin.php" class="btn">see admin</a>
                </div>

                <div class="box">
                    <?php
                    $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE admin_id = ?");
                    $select_orders->execute([$admin_id]);
                    $number_of_orders = $select_orders->rowCount();
                    ?>
                    <h3><?= $number_of_orders; ?></h3>
                    <p>total orders placed</p>
                    <a href="admin_order.php" class="btn">total orders</a>
                </div>

                <div class="box">
                    <?php
                    $select_confirm_orders = $conn->prepare("SELECT * FROM `orders` WHERE admin_id = ? AND status = ?");
                    $select_confirm_orders->execute([$admin_id, 'in progress']);
                    $number_of_confirm_orders = $select_confirm_orders->rowCount();
                    ?>
                    <h3><?= $number_of_confirm_orders; ?></h3>
                    <p>total confirm placed</p>
                    <a href="admin_order.php" class="btn">confirmed orders</a>
                </div>

                <div class="box">
                    <?php
                    $select_canceled_orders = $conn->prepare("SELECT * FROM `orders` WHERE admin_id = ? AND status = ?");
                    $select_canceled_orders->execute([$admin_id, 'in progress']);
                    $number_of_canceled_orders = $select_canceled_orders->rowCount();
                    ?>
                    <h3><?= $number_of_canceled_orders; ?></h3>
                    <p>total canceled placed</p>
                    <a href="admin_order.php" class="btn">canceled orders</a>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php
    include '../components/alert.php';
    ?>   

    <!-- custom js file link  -->
    <script src="../assets/script.js"></script>
</body>
</html>