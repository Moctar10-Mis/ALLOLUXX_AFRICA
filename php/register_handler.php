<?php
session_start();
include __DIR__ . '/config.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];

    if($password !== $confirm_password){
        $_SESSION['login_error'] = "Passwords do not match!";
        header("Location: ../register.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows>0){
        $_SESSION['already_registered'] = true;
        header("Location: ../register.php");
        exit();
    }

    $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users(full_name,email,password,gender) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$name,$email,$hashedPassword,$gender);
    if($stmt->execute()){
        $_SESSION['login_success']="Account created! Please login.";
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['login_error']="Registration failed!";
        header("Location: ../register.php");
        exit();
    }
}
else {
    header("Location: ../register.php");
    exit();
}