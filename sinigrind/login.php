<?php
session_start();
require_once 'db.php';  // only db.php

// Show alert if redirected from logout
if (isset($_GET['loggedout']) && $_GET['loggedout'] == 1) {
    echo "<script>alert('You are logged out');</script>";
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $users = runQuery("SELECT * FROM users WHERE email = ?", ["s", [$email]]);
    $user = $users ? $users[0] : null;

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid login.";
        $_SESSION['active_form'] = "login";
        header("Location: login.php");
        exit;
    }
}

// Handle register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email already exists
    $existing = runQuery("SELECT * FROM users WHERE email = ?", ["s", [$email]]);
    if ($existing) {
        $_SESSION['register_error'] = "Email already registered.";
        $_SESSION['active_form'] = "register";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        runQuery("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", ["sss", [$username, $email, $hashedPassword]]);
        $_SESSION['register_success'] = "Registration successful. Please login.";
        $_SESSION['active_form'] = "login";
    }
    header("Location: login.php");
    exit;
}

$successMessage = $_SESSION['register_success'] ?? '';
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login & Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Login Form -->
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login.php" method="post">
                <h2>Login</h2>
                <?= !empty($successMessage) ? "<p class='success-message'>$successMessage</p>" : ''; ?>
                <?= showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register">Register</button>
                <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

<?php
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['active_form']);
unset($_SESSION['register_success']);
?>