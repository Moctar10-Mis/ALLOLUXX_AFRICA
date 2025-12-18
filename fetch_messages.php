<?php
session_start();
include 'php/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) exit;

$result = $conn->query("SELECT * FROM support_messages WHERE user_id=$user_id ORDER BY created_at ASC");

while($row = $result->fetch_assoc()):
?>
<div class="msg-box <?= $row['sender']=='user'?'msg-user':'msg-admin' ?>" id="msg-<?= $row['id'] ?>">
    <strong><?= ucfirst($row['sender']) ?>:</strong>
    <?= htmlspecialchars($row['message']) ?><br>
    <small><?= $row['created_at'] ?></small>
    <?php if($row['sender']=='user'): ?>
        <span class="delete-btn" onclick="deleteMessage(<?= $row['id'] ?>)">Delete</span>
    <?php endif; ?>
</div>
<?php endwhile; ?>
