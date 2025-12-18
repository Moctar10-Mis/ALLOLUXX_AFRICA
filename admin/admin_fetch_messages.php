<?php
include 'php/config.php';
$user_id = intval($_GET['user_id']);

$msgs = $conn->query("
    SELECT * FROM support_messages
    WHERE user_id=$user_id
    ORDER BY created_at ASC
");

while($m=$msgs->fetch_assoc()){
    echo "<p><strong>{$m['sender']}:</strong> {$m['message']}</p>";
}
