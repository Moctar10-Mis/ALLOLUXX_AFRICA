<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include '../php/config.php';

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
<title>Admin Login - ALOLUXX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: times, 'Times New Roman', serif;
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

input[type="email"], input[type="password"] {
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

a {
    color: #007BFF;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}



/* Add the btn-back style here */
.btn-back {
    display: inline-block;
    background: #28a745;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
}
.btn-back:hover {
    background: #218838;
}


</style>
</head>
<body>

<div class="container">
    <h1>ALOLLUXX Admin Login</h1>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" value="<?= $_COOKIE['admin_email'] ?? '' ?>" required>
        <input type="password" name="password" placeholder="Password" value="<?= $_COOKIE['admin_pass'] ?? '' ?>" required>
        <label><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" name="login">Login</button>
    </form>
    <p style="margin-top: 20px;">No account? <a href="admin_register.php">Register here</a></p>
    <p style="margin-top: 20px; text-align:center;">
    <a href="../index.php" class="btn-back"> Go Back to Main Site</a>
</p>

</div>

</body>
</html>
