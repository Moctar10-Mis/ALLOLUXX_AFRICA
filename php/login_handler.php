<?php
session_start();
include __DIR__ . '/config.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['gender'] = $user['gender'];
        $_SESSION['full_name'] = $user['full_name'];

        if($remember){
            setcookie('email',$email,time()+30*24*60*60,'/');
            setcookie('password',$password,time()+30*24*60*60,'/');
        }

        if($user['gender']=='man') header("Location: ../man_dashboard.php");
        else header("Location: ../woman_dashboard.php");
        exit();
    } else {
        $_SESSION['login_error']="No account found or wrong password. Please register first.";
        header("Location: ../login.php");
        exit();
    }
}
