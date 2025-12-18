<?php
session_start();
include '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Both fields are required.";
        header("Location: admin_login.php");
        exit();
    }

    // Check admin in users table
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email=? AND is_admin=1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['full_name'];

            // Remember me
            if (!empty($_POST['remember'])) {
                setcookie('admin_email', $email, time()+3600*24*30, "/");
            }

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['login_error'] = "Admin not found.";
    }

    header("Location: admin_login.php");
    exit();
}
