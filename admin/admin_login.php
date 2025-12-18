<?php
session_start();
include '../php/config.php';

// Redirect already logged-in admins
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Get messages
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
$login_success = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css"> <!-- keep your pink styling -->
</head>
<body>

<div class="form-container">
    <div class="form-box">
        <h2>Admin Login</h2>
        <?php if($login_error): ?>
            <p class="message error"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>
        <?php if($login_success): ?>
            <p class="message success"><?= htmlspecialchars($login_success) ?></p>
        <?php endif; ?>
        <form action="admin_login_handler.php" method="POST">
            <div class="input-container">
                <input type="email" name="email" placeholder="Email Address" required value="<?= $_COOKIE['admin_email'] ?? '' ?>">
            </div>
            <div class="input-container">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-container">
                <label><input type="checkbox" name="remember"> Remember Me</label>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p><a href="../index.php">← Back to Main Site</a></p>
    </div>
</div>

</body>
</html>
