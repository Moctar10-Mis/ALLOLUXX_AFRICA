<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


// Remove remember me cookie if exists
if (isset($_COOKIE['admin_id'])) {
    setcookie('admin_id', '', time() - 3600, '/');
}

// Redirect safely to login page
header("Location: admin_login.php");
exit();
?>
