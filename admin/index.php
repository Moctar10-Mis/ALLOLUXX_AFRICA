<?php
session_start();
include '../php/config.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Check if user is admin
if (!$user || $user['is_admin'] != 1) {
    // Not admin → redirect
    header("Location: admin_login.php");
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Montserrat', sans-serif; background: linear-gradient(135deg,#fbd3e9,#bb377d); margin:0; }
header { background:#fff; padding:20px 40px; border-bottom-left-radius:15px; border-bottom-right-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.1); display:flex; justify-content:space-between; align-items:center;}
header h1 { margin:0; color:#a855f7; font-weight:800; text-transform:uppercase; letter-spacing:1px; font-size:28px; }
nav a { margin-left:10px; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; color:#fff; transition: all 0.3s ease; }
nav a.main { background:#34d399; }       
nav a.logout { background:#f472b6; }
.container { width:90%; max-width:900px; margin:50px auto; text-align:center; color:#6b21a8; }
.container h2 { font-size:28px; margin-bottom:20px; }
.container a { display:inline-block; margin:10px; padding:12px 20px; background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; border-radius:10px; text-decoration:none; font-weight:600; transition:0.3s; }
.container a:hover { opacity:0.9; transform:translateY(-2px); }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA Admin</h1>
    <nav>
        <a href="../index.php" class="main">Main Site</a>
        <a href="?logout" class="logout">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?></h2>
    <p>Select an action below to manage the site:</p>
    <a href="admin_dashboard.php">Dashboard Overview</a>
    <a href="add_product.php">Add Product</a>
    <a href="edit_product.php">Edit Products</a>
    <a href="orders.php">Manage Orders</a>
    <a href="manage_admins.php">Manage Admins</a>
</div>

</body>
</html>
