<?php
session_start();
include '../php/config.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        // Select admin from users table
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email=? AND is_admin=1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];

                // Remember me
                if (!empty($_POST['remember'])) {
                    setcookie('admin_email', $email, time()+3600*24*30, "/");
                    setcookie('admin_pass', $password, time()+3600*24*30, "/");
                }

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Admin not found.";
        }
    }
}
?>
