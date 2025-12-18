<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ===== Body & Background ===== */
body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #fbd3e9, #bb377d);
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.container {
    background: #ffffff;
    padding: 50px 40px;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    width: 400px;
    max-width: 90%;
    text-align: center;
}
.container h1 {
    font-size: 28px;
    font-weight: 800;
    color: #a855f7;
    margin-bottom: 25px;
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
label input[type="checkbox"] {
    margin-right: 8px;
}
button {
    width: 100%;
    padding: 14px 20px;
    margin-top: 15px;
    border: none;
    background: linear-gradient(90deg, #f472b6, #ec4899);
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
}
button:hover {
    background: linear-gradient(90deg, #ec4899, #db2777);
}
.error {
    background: #f87171;
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="container">
    <h1>ALLOLUX AFRICA</h1>

    <?php if ($login_error): ?>
        <div class="error"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_login_handler.php">
        <input type="email" name="email" placeholder="Email" value="<?= $_COOKIE['admin_email'] ?? '' ?>" required>
        <input type="password" name="password" placeholder="Password" value="<?= $_COOKIE['admin_pass'] ?? '' ?>" required>
        <label><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" name="login">Login</button>
    </form>

</div>

</body>
</html>
