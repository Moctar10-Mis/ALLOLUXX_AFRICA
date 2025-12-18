<?php
$host = "localhost";
$user = "moctar.issoufou";
$pass = "Mis10@.#";
$db = "webtech_2025A_moctar_issoufou";
//Mis10@.#
//webtech_2025A_moctar_issoufou
//moctar.issoufou
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
