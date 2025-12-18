<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../php/config.php';

// Initialize variable to avoid warnings
$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Remember me
                if (!empty($_POST['remember'])) {
                    setcookie('admin_email', $email, time()+3600*24*30, "/");
                    setcookie('admin_pass', $password, time()+3600*24*30, "/");
                }

                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Admin not found.";
        }
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
input[type="email"], input[type="password"] {
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

/* ===== Checkbox ===== */
label input[type="checkbox"] {
    margin-right: 8px;
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

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" value="<?= $_COOKIE['admin_email'] ?? '' ?>" required>
        <input type="password" name="password" placeholder="Password" value="<?= $_COOKIE['admin_pass'] ?? '' ?>" required>
        <label><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" name="login">Login</button>
    </form>

    <p class="footer-text">No account? <a href="admin_register.php">Register here</a></p>

    <a href="../index.php" class="btn-back"> Go Back to Main Site</a>
</div>

</body>
</html>
