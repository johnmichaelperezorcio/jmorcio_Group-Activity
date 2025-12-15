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
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SiniGrind Website</title>
        <!--Linking font awesome for icons-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <!-- Linking Bootstrap CSS-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <!-- Linking custom CSS-->
        <link rel="stylesheet" href="html_style.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    </head>
    <body>
        <header>
            <nav class="navbar section-content d-flex align-items-center justify-content-between">
                <!-- Logo on the left -->
                <a href="#" class="nav-logo d-flex align-items-center text-decoration-none">
                    <img src="assets/logo_iconpeanutbutter.png" alt="SiniGrind Logo" class="logo-image me-2" style="width: 60px;">
                    <h2 class="logo-text mb-0">SiniGrind</h2>
                </a>

                <!-- Centered navigation links -->
                <ul class="nav-menu d-flex align-items-center gap-3 mb-0">
                    <button class="fas fa-times" id="menu-close-button"></button>
                    <li class="nav-item"><a href="#coffee-product" class="nav-link">Coffee Products</a></li>
                    <li class="nav-item"><a href="#gallery" class="nav-link">Gallery</a></li>
                    <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="#contact" class="nav-link">Contact</a></li>
                </ul>

                <!-- Profile button on the right -->
                <div class="nav-buttons d-flex gap-2">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileSidebar" aria-controls="profileSidebar">
                    <i class="fas fa-user"></i> Profile
                </button>
                </div>

                <!-- Mobile menu toggle -->
                <button class="fas fa-bars" id="menu-open-button"></button>
            </nav>
        </header>

        <!-- Profile Sidebar -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="profileSidebar" aria-labelledby="profileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="profileSidebarLabel">My Profile</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column gap-3">
            <a href="cart_view.php" class="btn btn-outline-primary w-100">My Cart</a>
            <a href="my_orders.php" class="btn btn-outline-primary w-100">My Orders</a>
            <a href="logout.php" class="btn btn-danger w-100">Logout</a>
        </div>
        </div>

        
        <main>
            <!--Menu section-->
            <section class="menu-section" id="coffee-product">
                <div class="container py-4">
                    <h2 class="section-title">Coffee Products</h2>
                    <div class="row gy-4">
                    <?php foreach ($products as $p): ?>
                    <div class="col-md-4">
                        <!-- Add p-3 for padding inside the card -->
                        <div class="card shadow-sm mb-4 h-100 p-3">
                        <?php if (!empty($p['photo'])): ?>
                            <img src="uploads/<?= htmlspecialchars($p['photo']) ?>" 
                                class="card-img-top mb-3" 
                                alt="Product Photo" 
                                style="height:200px;object-fit:cover;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/200x200?text=No+Image" 
                                class="card-img-top mb-3" 
                                alt="No Photo">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column p-0">
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
            </section>


            <!-- Gallery Section -->
            <section class="gallery-section py-5" id="gallery">
            <h2 class="section-title text-uppercase text-center mb-5">Gallery</h2>
                <div class="container">
                    <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-1.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-2.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-3.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-4.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-5.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="gallery-item rounded overflow-hidden" style="height: 300px;">
                        <img src="assets/gallery-6.jpg" alt="Gallery" class="gallery-image img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    </div>
                </div>
            </section>

            <!-- About section -->
            <section class="about-section" id="about">
                <div class="section-content">
                    <div class="about-image-wrapper">
                        <img src="assets/sinigang.jpg" alt="About" class="about-image">
                    </div>
                    <div class="about-details">
                        <h2 class="section-title">About Us</h2>
                        <p class="text">SiniGrind is a pride itself, serving you exceptional coffee quality, the go-to for coffee lovers and caffeine addict alike. We're dedicated to providing only the best coffee drink and feel the comfort, like home.</p>
<!--
                        <div class="social-link-list">
                            <a href="#" class="social-link"><i class="fa-brands fa-facebook"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fa-brands fa-x-twitter"></i></a>
                        </div> -->
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="contact-section" id="contact">
                <h2 class="section-title">Contact Us</h2>
                <div class="section-content d-flex justify-content-center">
                    <ul class="contact-info-list">
                        <li class="contact-info">
                            <i class="fa-solid fa-location-crosshairs" style="display: flex; align-items: center;"></i>
                            <p style="margin: 0; display: flex; align-items: center;">0123 Centro Occidental, Polangui, 4506 Albay, Philippines</p>
                        </li>
                        <li class="contact-info">
                            <i class="fa-regular fa-envelope" style="display: flex; align-items: center;"></i>
                            <p style="margin: 0; display: flex; align-items: center;">sinigrindcoffee@gmail.com</p>
                        </li>
                        <li class="contact-info">
                            <i class="fa-solid fa-phone" style="display: flex; align-items: center;"></i>
                            <p style="margin: 0; display: flex; align-items: center;">(+63) 912-345-6789</p>
                        </li>
                        <li class="contact-info">
                            <i class="fa-regular fa-clock" style="display: flex; align-items: center;"></i>
                            <div style="margin: 0; display: flex; flex-direction: column; align-items: flex-start;">
                                <p style="margin: 0;">Monday - Saturday: 9:00 AM - 5:00 PM</p>
                                <p style="margin: 0;">Sunday: Closed</p>   
                            </div>
                        </li>
                        <li class="contact-info">
                            <i class="fa-solid fa-globe"></i>
                            <p style="margin: 0; display: flex; align-items: center;">www.sinigrindcoffee.com</p>
                        </li>
                    </ul>
                </div>
<!--
                    <form action="#" class="contact-form">
                        <input type="text" class="form-input" placeholder="Your name" required>
                        <input type="email" class="form-input" placeholder="Your email" required>
                        <textarea placeholder="Your message" class="form-input" required></textarea>
                        <button class="submit-button">Submit</button>
                    </form>-->
                </div>
            </section>

            <!-- Footer Section -->
            <footer class="footer-section">
                <div class="section-content d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="footer-text mb-0">© 2024 SiniGrind. All rights reserved.</p>
                    
                    <div class="social-link-list">
                        <a href="#" class="social-link"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>

                    <p class="policy-text mb-0">
                        <a href="#" class="policy-link">Privacy policy</a>
                        <span class="separator">⦁</span>
                        <a href="#" class="policy-link">Refund policy</a>
                    </p>
                </div>

            </footer>

        </main>

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

            const navLinks = document.querySelectorAll (".nav-link");
            const menuOpenButton = document.querySelector ("#menu-open-button");
            const menuCloseButton = document.querySelector ("#menu-close-button");

            menuOpenButton.addEventListener('click', () => {
                // Toggle the mobile menu visibility
                document.body.classList.toggle("show-mobile-menu");
            });

            //Close the mobile menu when the close button is clicked
            menuCloseButton.addEventListener('click', () => menuOpenButton.click() );

            // Close menu when the nav link is clicked
            navLinks.forEach(link => {
                link.addEventListener('click', () => menuOpenButton.click());
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>