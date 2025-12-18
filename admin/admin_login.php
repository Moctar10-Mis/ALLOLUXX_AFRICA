<?php
session_start();
include '../php/config.php';

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg,#fbd3e9,#bb377d);
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.form-container {
    background: #fff;
    padding: 50px 40px;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    width: 400px;
    max-width: 90%;
    text-align: center;
}

.form-container h1 {
    font-size: 28px;
    font-weight: 800;
    color: #a855f7;
    margin-bottom: 25px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

input[type="email"], input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    font-size: 15px;
}

input:focus {
    outline: none;
    border-color: #f472b6;
    box-shadow: 0 0 8px rgba(244,114,182,0.4);
}

label input[type="checkbox"] { margin-right: 8px; }

button {
    width: 100%;
    padding: 14px 20px;
    margin-top: 15px;
    border: none;
    background: linear-gradient(90deg,#f472b6,#ec4899);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
}

button:hover { opacity:0.9; transform: translateY(-2px); }

.error {
    background: #f87171;
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
}

a { color: #a855f7; font-weight: 600; text-decoration: none; }
a:hover { text-decoration: underline; }

.btn-back {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: linear-gradient(90deg,#f472b6,#ec4899);
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}

.btn-back:hover { opacity:0.9; transform: translateY(-2px); }

p.footer-text { margin-top: 20px; font-size: 14px; color: #4b5563; }

</style>
</head>
<body>

<div class="form-container">
    <h1>ALLOLUX AFRICA Admin</h1>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" action="admin_login_handler.php">
        <input type="email" name="email" placeholder="Email" required value="<?= $_COOKIE['admin_email'] ?? '' ?>">
        <input type="password" name="password" placeholder="Password" required value="<?= $_COOKIE['admin_pass'] ?? '' ?>">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" name="login">Login</button>
    </form>

    <p class="footer-text">No account? <a href="admin_register.php">Register here</a></p>

    <a href="../index.php" class="btn-back">Go Back to Main Site</a>
</div>

</body>
</html>
