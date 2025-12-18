<?php
session_start();
include 'php/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$msg_id  = intval($_GET['id']);

if($user_id && $msg_id){
    $stmt = $conn->prepare("DELETE FROM support_messages WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $msg_id, $user_id);
    $stmt->execute();
}

header("Location: contact.php");
exit;
