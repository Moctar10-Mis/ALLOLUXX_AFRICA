<?php
session_start();
include ('php/config.php');

// Redirect logged-in users to their dashboard
if(isset($_SESSION['user_id']) && isset($_SESSION['gender'])){
    if($_SESSION['gender']=='man') header("Location: man_dashboard.php");
    else header("Location: woman_dashboard.php");
    exit();
}

// Check if user already registered (via session flag)
$already_registered = $_SESSION['already_registered'] ?? false;
unset($_SESSION['already_registered']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - ALOLLUXX AFRICA</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script>
// Disable browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // user cannot go back
};
</script>

</head>

<body>

<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav>
            <a href="register.php" class="btn btn-primary">Register</a>
            <a href="login.php" class="btn">Login</a>
            <a href="logout.php" class="btn btn-primary">Logout</a>
            <a href="index.php" class="btn btn-secondary"> <- Go Back</a>

        </nav>
    </div>
</header>

<div class="form-container">
    <div class="form-box">
        <?php if($already_registered): ?>
            <h2>Account Exists</h2>
            <p class="message error">You already have an account! Please login.</p>
            <a href="login.php"><button>Go to Login</button></a>
        <?php else: ?>
            <h2>Create Your Account</h2>
            <form action="php/register_handler.php" method="POST">
                <div class="input-container">
                    <input type="text" name="full_name" placeholder="Full Name" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-container">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div class="input-container">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <div id="password-strength" class="password-strength"></div>
                <button type="button" id="generatePassword" class="btn-small">Suggest Password</button>
                <div class="input-container">
                    <select name="gender" required>
                        <option value="">-- Choose One --</option>
                        <option value="man">Man</option>
                        <option value="woman">Woman</option>
                    </select>
                    <i class="fas fa-venus-mars"></i>
                </div>
                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
// Password strength checker
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm_password');
const strengthText = document.getElementById('password-strength');

password.addEventListener('input', () => {
    const val = password.value;
    let strength = '';
    if(val.length < 6) strength = 'Weak';
    else if(val.length < 10) strength = 'Moderate';
    else strength = 'Strong';
    strengthText.textContent = 'Password Strength: ' + strength;
});

// Suggest password
const generateBtn = document.getElementById('generatePassword');
generateBtn.addEventListener('click', () => {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*";
    let pass = '';
    for(let i=0;i<12;i++) pass += chars[Math.floor(Math.random()*chars.length)];
    password.value = pass;
    confirmPassword.value = pass;
    strengthText.textContent = 'Password Strength: Strong';
});
</script>
</body>
</html>
