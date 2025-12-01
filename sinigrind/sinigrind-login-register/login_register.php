<?php

session_start();
require_once 'conn.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $checkStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkEmail = $checkStmt->get_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = "Email is already registered!";
        $_SESSION['active_form'] = 'register';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();

        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        $_SESSION['active_form'] = 'login';
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
            // 🔍 Debug logs
            error_log("Entered password: $password");
            error_log("Stored hash: {$user['password']}");
            
            if ($password === $user['password']) {
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                if ($user['role'] === 'admin') {
                    header("Location: admin_page.php");
                } else {
                    header("Location: user_page.php");
                }
                exit();
            }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

?>