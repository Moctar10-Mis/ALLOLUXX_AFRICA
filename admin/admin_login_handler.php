<?php
session_start();
include '../php/config.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if ($email === '' || $password === '') {
    $_SESSION['login_error'] = "Both fields are required.";
    header("Location: admin_login.php");
    exit();
}

// Prepare and fetch admin
$stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ? AND is_admin = 1 LIMIT 1");
if (!$stmt) {
    error_log("admin_login_handler: prepare failed - " . $conn->error);
    $_SESSION['login_error'] = "Server error. Try again later.";
    header("Location: admin_login.php");
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        // Set session and redirect to dashboard
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['full_name'];

        // Optional: remember email only (avoid storing plain password)
        if (!empty($_POST['remember'])) {
            setcookie('admin_email', $email, time() + (3600*24*30), "/");
        }

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Incorrect password.";
        header("Location: admin_login.php");
        exit();
    }
} else {
    $_SESSION['login_error'] = "Admin not found.";
    header("Location: admin_login.php");
    exit();
}