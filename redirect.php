<?php
session_start();
if(!isset($_GET['gender'])) exit("Invalid access");
$gender = $_GET['gender'];

// If user not logged in → first-time user → register
if(!isset($_SESSION['user_id'])){
    header("Location: register.php");
    exit();
}

// If logged in, check category
$user_gender = $_SESSION['gender'];
if($user_gender != $gender){
    echo "<script>alert('This is not your category!'); window.location='index.php';</script>";
    exit();
}

// Redirect to dashboard
if($user_gender=='man') header("Location: man_dashboard.php");
else header("Location: woman_dashboard.php");
exit();
