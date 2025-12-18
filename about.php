<?php
session_start();
include 'php/config.php';

// Optional: show "Hello, user" if logged in
$user_name = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user = $conn->query("SELECT full_name FROM users WHERE id=$user_id")->fetch_assoc();
    if ($user) $user_name = $user['full_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Montserrat',sans-serif; margin:0; background:linear-gradient(135deg,#fbd3e9,#bb377d); color:#6b21a8; }
header { background:#fff; padding:20px 40px; border-bottom-left-radius:15px; border-bottom-right-radius:15px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 6px 20px rgba(0,0,0,0.1);}
header h1 { margin:0; color:#a855f7; font-weight:800; text-transform:uppercase; }
nav a { margin-left:10px; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; color:#fff; background:#f472b6; transition:0.3s;}
nav a:hover { opacity:0.9; }
.container { width:90%; max-width:1000px; margin:50px auto; background:rgba(255,255,255,0.95); padding:30px; border-radius:15px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; font-size:32px; margin-bottom:20px; color:#a855f7; }
p, li { line-height:1.6; font-size:16px; }
ul { margin-left:20px; }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if ($user_name): ?>
            <span>Hello, <?= htmlspecialchars($user_name) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h2>About the Application</h2>
    <p>ALLOLUX AFRICA is an online fashion store that works like a real physical boutique:</p>
    <ul>
        <li>Intuitive navigation</li>
        <li>Direct interaction with the seller via chat</li>
        <li>Orders validated through conversation</li>
        <li>Secure integrated payment</li>
        <li>Filter products by Men/Women categories and subcategories to easily find items</li>
    </ul>

    <h3>Main Goals</h3>
    <ul>
        <li>Sell men's and women's clothing in a simple, interactive way</li>
        <li>Create a physical boutique-like experience via live chat</li>
        <li>Help customers make decisions with clear product presentation</li>
        <li>Make order and payment management easy</li>
    </ul>
</div>

</body>
</html>
