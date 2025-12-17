<?php
session_start();
require __DIR__ . '/php/config.php';

// Dashboard function
function dashboard() {
    global $conn;

    if(isset($_SESSION['user_id'], $_SESSION['user_type'])){
        return ($_SESSION['user_type']=='woman') ? 'woman_dashboard.php' : 'man_dashboard.php';
    }

    if(isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
        $stmt = $conn->prepare("SELECT user_type FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_type);
        if($stmt->fetch()){
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;
            return ($user_type=='woman') ? 'woman_dashboard.php' : 'man_dashboard.php';
        }
    }

    return 'register.php';
}

// Auto-redirect logged-in users
$dashboardLink = dashboard();
if($dashboardLink != 'register.php'){
    header("Location: $dashboardLink");
    exit;
}

$loggedIn = isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ALOLLUXX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
* {margin:0;padding:0;box-sizing:border-box;}
body {height:100vh;font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#FFF6F9,#FFE4EC);color:#2B2B2B;}
header {position:absolute;top:0;width:100%;padding:30px 80px;display:flex;justify-content:space-between;align-items:center;}
.logo {font-family:'Playfair Display',serif;font-size:32px;font-weight:700;color:#E91E63;}
nav a {margin-left:30px;text-decoration:none;color:#2B2B2B;font-weight:500;}
nav a:hover {color:#E91E63;}
.hero {height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;padding:0 20px;}
.hero h1 {font-family:'Playfair Display',serif;font-size:64px;margin-bottom:20px;color:#2B2B2B;}
.hero p {max-width:600px;font-size:18px;color:#555;margin-bottom:50px;}
.buttons {display:flex;gap:25px;}
.buttons a {padding:16px 40px;border-radius:40px;text-decoration:none;font-weight:500;font-size:16px;transition:0.3s ease;}
.woman {background:#E91E63;color:white;}
.woman:hover {background:#c2185b;}
.man {border:2px solid #E91E63;color:#E91E63;background:transparent;}
.man:hover {background:#E91E63;color:white;}
.footer-note {position:absolute;bottom:25px;font-size:14px;color:#999;}
</style>
</head>
<body>

<header>
    <div class="logo">ALOLLUXX AFRICA</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="help.php">Help</a>
        <a href="contact.php">Contact</a>
        <a href="architecture.php">Architecture</a>
        <?php if(!$loggedIn): ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <h1>African Elegance</h1>
    <p>Discover timeless African fashion designed for confidence, beauty, and modern identity.</p>

    <div class="buttons">
        <a href="register.php" class="woman">Woman Dashboard</a>
        <a href="register.php" class="man">Man Dashboard</a>
    </div>
</section>

<div class="footer-note">© 2025 ALOLUXX AFRICA — Crafted with elegance</div>

</body>
</html>
