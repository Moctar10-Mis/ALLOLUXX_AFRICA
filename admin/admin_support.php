<?php
session_start();
include '../php/config.php';

/* ==========================
   ADMIN AUTH PROTECTION
========================== */
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

/* ==========================
   HANDLE ADMIN REPLY
========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $reply   = trim($_POST['reply']);
    $user_id = intval($_POST['user_id']);
    $admin_id = $_SESSION['admin_id'];

    if (!empty($reply)) {
        $stmt = $conn->prepare("
            INSERT INTO support_messages (user_id, admin_id, sender, message)
            VALUES (?, ?, 'admin', ?)
        ");
        $stmt->bind_param("iis", $user_id, $admin_id, $reply);
        $stmt->execute();
    }
}

/* ==========================
   FETCH USERS WHO SENT MESSAGES
========================== */
$users = $conn->query("
    SELECT DISTINCT user_id 
    FROM support_messages 
    WHERE user_id IS NOT NULL
    ORDER BY user_id ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Support | ALLOLUX AFRICA</title>
<style>
body{
    font-family: 'Montserrat', Arial, sans-serif;
    background: linear-gradient(135deg,#fbd3e9,#bb377d);
    margin:0;
    padding:20px;
    color:#6b21a8;
}
h2{text-align:center;margin-bottom:30px;}
.chat-box{
    background:#fff;
    padding:20px;
    border-radius:15px;
    margin-bottom:25px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
}
.messages{
    max-height:300px;
    overflow-y:auto;
    margin-bottom:15px;
}
.msg-user{
    background:#f9e6f6;
    padding:10px;
    border-radius:10px;
    margin-bottom:8px;
}
.msg-admin{
    background:#a855f7;
    color:#fff;
    padding:10px;
    border-radius:10px;
    margin-bottom:8px;
    text-align:right;
}
textarea{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
    resize:none;
}
button{
    margin-top:10px;
    padding:10px 18px;
    border:none;
    border-radius:10px;
    background:linear-gradient(90deg,#f472b6,#ec4899);
    color:#fff;
    font-weight:600;
    cursor:pointer;
}
button:hover{opacity:.9}
small{opacity:.7}
</style>
</head>
<body>

<div style="
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 25px;
    background:#ffffff;
    border-radius:12px;
    margin:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
">
    <h2 style="margin:0;color:#6b21a8;">Admin Support</h2>

    <a href="admin_dashboard.php"
       style="
        background:linear-gradient(90deg,#f472b6,#ec4899);
        color:#fff;
        padding:10px 18px;
        border-radius:8px;
        text-decoration:none;
        font-weight:600;
       ">
        Back to Dashboard
    </a>
</div>

<h2>💬 Customer Support Messages</h2>

<?php if ($users && $users->num_rows > 0): ?>
    <?php while($u = $users->fetch_assoc()): ?>
    <div class="chat-box">
        <h4>User ID: <?= $u['user_id'] ?></h4>

        <div class="messages" id="chat<?= $u['user_id'] ?>">
            <?php
            $userId = (int)$u['user_id'];
            $msgs = $conn->query("
                SELECT * FROM support_messages
                WHERE user_id = $userId
                ORDER BY created_at ASC
            ");
            if ($msgs && $msgs->num_rows > 0):
                while($m = $msgs->fetch_assoc()):
            ?>
                    <div class="<?= $m['sender'] === 'admin' ? 'msg-admin' : 'msg-user' ?>">
                        <strong><?= ucfirst($m['sender']) ?>:</strong>
                        <?= htmlspecialchars($m['message']) ?><br>
                        <small><?= $m['created_at'] ?></small>
                    </div>
            <?php
                endwhile;
            else:
            ?>
                <p style="color:#999;">No messages yet.</p>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
            <textarea name="reply" rows="3" placeholder="Type your reply..." required></textarea>
            <button type="submit">Send Reply</button>
        </form>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;color:#fff;font-weight:600;">No users have sent messages yet.</p>
<?php endif; ?>

</body>
</html>
