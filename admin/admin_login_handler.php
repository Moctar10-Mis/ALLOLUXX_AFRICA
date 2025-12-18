<?php
session_start();
include '../php/config.php';

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $_SESSION['admin_login_error'] = "Both fields are required.";
        header("Location: admin_login.php");
        exit();
    }

    // Check admin table
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $admin = $result->fetch_assoc();
        // Compare passwords (if you hashed it with password_hash)
        if(password_verify($password, $admin['password'])){
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            // Remember me cookies
            if(!empty($_POST['remember'])){
                setcookie('admin_email', $email, time()+3600*24*30, "/");
                setcookie('admin_pass', $password, time()+3600*24*30, "/");
            }

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['admin_login_error'] = "Incorrect password.";
            header("Location: admin_login.php");
            exit();
        }
    } else {
        $_SESSION['admin_login_error'] = "Admin not found.";
        header("Location: admin_login.php");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
