<?php
session_start();
include '../php/config.php';

// Only allow logged-in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error_msg = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_msg = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("sss", $username, $email, $hashed);
            if ($stmt->execute()) {
                $success_msg = "New admin created successfully!";
            } else {
                $error_msg = "Failed to create admin.";
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Add New Admin</h2>

    <?php if($success_msg) echo "<div class='success'>$success_msg</div>"; ?>
    <?php if($error_msg) echo "<div class='error'>$error_msg</div>"; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="add_admin" class="btn btn-primary">Create Admin</button>
    </form>
</div>
</body>
</html>
