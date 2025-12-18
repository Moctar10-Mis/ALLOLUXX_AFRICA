<?php
session_start();
include 'php/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$user_name = '';

// Get logged-in user name
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_name = $row['full_name'];
    }
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message && $user_id) {
        $stmt = $conn->prepare("INSERT INTO support_messages (user_id, sender, message, created_at) VALUES (?, 'user', ?, NOW())");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Montserrat',sans-serif; margin:0; background:linear-gradient(135deg,#fbd3e9,#bb377d); color:#6b21a8; }
header { background:#fff; padding:20px 40px; border-bottom-left-radius:15px; border-bottom-right-radius:15px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 6px 20px rgba(0,0,0,0.1);}
header h1 { margin:0; color:#a855f7; font-weight:800; text-transform:uppercase; }
nav a, nav span { margin-left:10px; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; color:#fff; background:#f472b6; transition:0.3s;}
nav a:hover { opacity:0.9; }
.container { width:90%; max-width:600px; margin:50px auto; background:rgba(255,255,255,0.95); padding:30px; border-radius:15px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; font-size:32px; margin-bottom:20px; color:#a855f7; }
form textarea { width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #d1d5db; resize:none;}
form button { background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; }
.msg-box { margin:10px 0; padding:10px; border-radius:8px; position:relative; }
.msg-user { background:#fde2f3; }
.msg-admin { background:#e0f2fe; }
.delete-btn { color:red; text-decoration:none; font-weight:600; cursor:pointer; position:absolute; right:10px; top:10px; }
.contact-info { margin-top:30px; padding:20px; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if ($user_name): ?>
            <span>Hello, <?= htmlspecialchars($user_name) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h2>Contact Support</h2>

    <?php if ($user_id): ?>
        <form method="POST">
            <textarea name="message" rows="4" placeholder="Type your message..." required></textarea>
            <button type="submit">Send Message</button>
        </form>

        <h3>Your Messages</h3>
        <div id="chatBox"></div>

        <script>
        // Load messages from server
        function loadMessages(){
            fetch('fetch_messages.php')
                .then(res => res.text())
                .then(data => { document.getElementById('chatBox').innerHTML = data; });
        }
        setInterval(loadMessages, 2000); // Refresh every 2 seconds
        loadMessages();

        // Delete message
        function deleteMessage(id){
            if(confirm('Are you sure you want to delete this message?')){
                fetch('delete_message.php?id=' + id)
                .then(res => res.text())
                .then(()=> {
                    const msgDiv = document.getElementById('msg'+id);
                    if(msgDiv) msgDiv.remove();
                });
            }
        }
        </script>

    <?php else: ?>
        <p>Please login to send messages to admin.</p>
    <?php endif; ?>

    <div class="contact-info">
        <h3>Our Contact Info</h3>
        <p>Email: moctarissoufousalifou@gmail.com</p>
        <p>Phone: +233592465945</p>
        <p>Locations: Niger, Ghana</p>
    </div>
</div>

</body>
</html>
