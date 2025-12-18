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
<title>Admin Register - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ===== Body & Background ===== */
body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #fbd3e9, #bb377d); /* soft pastel pink-purple gradient */
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* ===== Container Card ===== */
.container {
    background: #ffffff;
    padding: 50px 40px;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    width: 400px;
    max-width: 90%;
    text-align: center;
    position: relative;
}

/* ===== Branding Header ===== */
.container h1 {
    font-size: 28px;
    font-weight: 800;
    color: #a855f7; /* soft purple */
    margin-bottom: 25px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ===== Inputs ===== */
input {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    font-size: 15px;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #f472b6; /* pink highlight */
    box-shadow: 0 0 8px rgba(244,114,182,0.4);
}

/* ===== Buttons ===== */
button {
    width: 100%;
    padding: 14px 20px;
    margin-top: 15px;
    border: none;
    background: linear-gradient(90deg, #f472b6, #ec4899); /* pink gradient */
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background: linear-gradient(90deg, #ec4899, #db2777);
    transform: translateY(-2px);
}

/* ===== Messages ===== */
.error {
    background: #f87171; /* soft red */
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
}

.success {
    background: #34d399; /* soft green */
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
}

/* ===== Links ===== */
a {
    color: #a855f7; /* soft purple */
    text-decoration: none;
    font-weight: 600;
}

a:hover {
    text-decoration: underline;
}

/* ===== Back Button ===== */
.btn-back {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: linear-gradient(90deg, #f472b6, #ec4899);
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: linear-gradient(90deg, #ec4899, #db2777);
    transform: translateY(-2px);
}

/* ===== Footer Text ===== */
p.footer-text {
    margin-top: 20px;
    font-size: 14px;
    color: #4b5563;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .container {
        padding: 35px 25px;
    }
    h1 {
        font-size: 24px;
    }
    button, .btn-back {
        font-size: 14px;
        padding: 12px 15px;
    }
}
</style>
</head>
<body>

<div class="container">
    <h1>ALLOLUX AFRICA</h1>

    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="register">Register</button>
    </form>

    <p class="footer-text">Already have an account? <a href="admin_login.php">Login here</a></p>

    <a href="../index.php" class="btn-back">  Go Back to Main Site</a>
</div>

</body>
</html>
