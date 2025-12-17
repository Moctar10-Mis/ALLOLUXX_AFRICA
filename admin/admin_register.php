<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../php/config.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert admin
            $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);
            $stmt->execute();

            $success = "Admin registered successfully! <a href='admin_login.php'>Login here</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Register - ALOLUXX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: times, 'Times New Roman', Times, serif;
    background: linear-gradient(to right, #007BFF, #00BFFF);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #fff;
    padding: 40px 50px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    width: 400px;
    text-align: center;
}

h1 {
    font-size: 32px;
    font-weight: 700;
    color: #007BFF;
    margin-bottom: 30px;
}

input {
    width: 100%;
    padding: 12px 15px;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    border: none;
    background: #007BFF;
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}

.error {
    color: #dc3545;
    margin-bottom: 15px;
}

.success {
    color: #28a745;
    margin-bottom: 15px;
}

a {
    color: #007BFF;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

a:hover {
    text-decoration: underline;
}


.btn-back {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #28a745;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}
</style>
</head>
<body>

<div class="container">
    <h1>ALOLLUXX Admin Register</h1>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="register">Register</button>
    </form>
    <p style="margin-top: 20px;">Already have an account? <a href="admin_login.php">Login here</a></p>
    </div><p style="margin-top: 20px; text-align:center;">
    <a href="../index.php" class="btn-back">  Go Back to Main Site</a>
</p>


</body>
</html>
