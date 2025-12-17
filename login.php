<?php
session_start();
include _('php/config.php');

// Redirect logged-in users
if(isset($_SESSION['user_id']) && isset($_SESSION['gender'])){
    if($_SESSION['gender']=='man') header("Location: man_dashboard.php");
    else header("Location: woman_dashboard.php");
    exit();
}

$email_cookie = $_COOKIE['email'] ?? '';
$password_cookie = $_COOKIE['password'] ?? '';
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
$login_success = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - ALOLLUXX AFRICA</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<script>
// Disable browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // user cannot go back
};
</script>
<body>
<header>



    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav>
            <a href="register.php" class="btn btn-primary">Register</a>
            <a href="login.php" class="btn">Login</a>
            <a href="logout.php" class="btn btn-primary">Logout</a>
            <a href="index.php" class="btn btn-secondary">← Back</a>
        </nav>
    </div>
</header>



<div class="form-container">
    <div class="form-box">
        <h2>Login</h2>
        <?php if($login_error): ?>
            <p class="message error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <?php if($login_success): ?>
            <p class="message success"><?php echo $login_success; ?></p>
        <?php endif; ?>
        <form action="php/login_handler.php" method="POST">
            <div class="input-container">
                <input type="email" name="email" placeholder="Email Address" required value="<?php echo $email_cookie; ?>">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-container">
                <input type="password" name="password" placeholder="Password" required value="<?php echo $password_cookie; ?>">
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-container">
                <label><input type="checkbox" name="remember"> Remember Me</label>
            </div>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
</div>





</body>
</html>
