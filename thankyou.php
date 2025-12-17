<?php
session_start();
include 'php/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get order success message
$order_success = $_SESSION['order_success'] ?? '';
unset($_SESSION['order_success']); // Clear message after showing
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - ALOLLUXX AFRICA</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .message { margin: 50px auto; width: 80%; text-align: center; padding: 20px; border: 1px solid #ccc; background: #f9f9f9; font-size: 1.2em; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007BFF; color: #fff; text-decoration: none; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav class="top-actions">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </nav>
    </div>
</header>

<div class="message">
    <?php echo htmlspecialchars($order_success ?: "Thank you! Your order has been placed successfully."); ?>
    <br>
    <a href="man_dashboard.php" class="btn">Back to Dashboard</a>
    <a href="purchases.php" class="btn">View Purchases</a>
</div>
</body>
</html>
