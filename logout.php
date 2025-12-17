<?php
session_start();
session_unset();
session_destroy();

// Clear cookies
setcookie('user_id', '', time() - 3600, "/");
setcookie('gender', '', time() - 3600, "/");
setcookie('email', '', time() - 3600, "/");
setcookie('password', '', time() - 3600, "/");

header("Location: index.php");
exit();
?>
