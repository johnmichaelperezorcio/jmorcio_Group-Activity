<?php
session_start();
require_once 'db.php';  // only db.php, no sql_utils.php

// Show alert if redirected from logout
if (isset($_GET['loggedout']) && $_GET['loggedout'] == 1) {
    echo "<script>alert('You are logged out');</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user by username
    $users = runQuery("SELECT * FROM users WHERE username = ?", ["s", [$username]]);
    $user = $users ? $users[0] : null;

    if ($user && $password === $user['password']) { // plain text version
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid login.";
    }
}
?>
<form method="post">
  <input type="text" name="username" required>
  <input type="password" name="password" required>
  <button>Login</button>
</form>