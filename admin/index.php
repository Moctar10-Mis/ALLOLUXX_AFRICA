<?php
session_start();

// PREVENT CACHING 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect if not logged in 
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

//Logout 
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - ALOLUXX AFRICA</title>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
}
header {
    background-color: #007BFF;
    color: white;
    padding: 20px;
    text-align: center;
}
nav {
    margin-top: 15px;
}
nav a {
    text-decoration: none;
    color: white;
    background-color: #28a745;
    padding: 8px 15px;
    border-radius: 5px;
    margin-right: 10px;
}
nav a.logout {
    background-color: #dc3545;
}
.container {
    text-align: center;
    margin-top: 50px;
}
</style>
</head>

<body>

<header>
    <h1>ALLOLUXX AFRICA – Admin Panel</h1>
    <nav>
        <a href="../index.php">Go to Main Site</a>
        <a href="?logout=1" class="logout">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Welcome, Admin</h2>
    <p>Select an action from the admin dashboard.</p>
</div>

<!--  BACK BUTTON BLOCK -->
<script>
(function () {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        window.history.pushState(null, "", window.location.href);
    };
})();
</script>

</body>
</html>
