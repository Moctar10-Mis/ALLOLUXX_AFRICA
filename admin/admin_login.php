<?php
session_start();
include '../php/config.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ? AND is_admin = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['full_name'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "Admin not found.";
    }
}
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
    background: linear-gradient(135deg, #fbd3e9, #bb377d);
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.login-container {
    background: rgba(255,255,255,0.95);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 350px;
    text-align: center;
}
.login-container h1 {
    color: #a855f7;
    margin-bottom: 30px;
    font-size: 28px;
    font-weight: 800;
    text-transform: uppercase;
}
.login-container input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}
.login-container button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(90deg,#f472b6,#ec4899);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}
.login-container button:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}
.login-container p.error {
    color: #ec4899;
    font-weight: 600;
    margin-top: 15px;
}
</style>
</head>
<body>

<div class="login-container">
    <h1>Admin Login</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if($login_error): ?>
        <p class="error"><?= htmlspecialchars($login_error) ?></p>
    <?php endif; ?>
</div>

</body>
</html>
