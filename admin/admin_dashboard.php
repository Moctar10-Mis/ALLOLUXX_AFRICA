<?php
session_start();
include '../php/config.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Make sure user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// Fetch stats
$totalUsers = $conn->query("SELECT COUNT(*) as total_users FROM users")->fetch_assoc()['total_users'];
$totalOrders = $conn->query("SELECT COUNT(*) as total_orders FROM orders")->fetch_assoc()['total_orders'];

// Latest orders
$latestOrdersQuery = $conn->query("
    SELECT o.id AS order_id, o.total_price, o.status, o.created_at, o.payment_method,
           u.email, u.gender, u.full_name AS user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Latest single order
$latestOrder = $conn->query("
    SELECT o.id AS order_id, o.total_price, o.status, o.payment_method, o.created_at,
           u.full_name AS user_name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 1
")->fetch_assoc();

// Items for latest order
$orderItems = [];
if ($latestOrder) {
    $orderId = $latestOrder['order_id'];
    $itemsResult = $conn->query("
        SELECT p.name, p.image, p.category, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $orderId
    ");
    while ($row = $itemsResult->fetch_assoc()) $orderItems[] = $row;
}

// Orders per user
$ordersPerUserQuery = $conn->query("
    SELECT u.full_name AS user_name, u.email, COUNT(o.id) AS orders_count
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY orders_count DESC
");

// Products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Fetch current admins
$admins = $conn->query("SELECT id, email, full_name, created_at FROM users WHERE is_admin = 1 ORDER BY created_at DESC");

// Handle adding new admin
$admin_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        $admin_msg = "All fields are required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $admin_msg = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, is_admin, created_at) VALUES (?, ?, ?, 1, NOW())");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $admin_msg = "New admin added successfully!";
                // Refresh admins list
                $admins = $conn->query("SELECT id, email, full_name, created_at FROM users WHERE is_admin = 1 ORDER BY created_at DESC");
            } else {
                $admin_msg = "Failed to add new admin.";
            }
        }
    }
}

// Unread support messages
$unread = $conn->query("
    SELECT COUNT(*) AS total 
    FROM support_messages 
    WHERE sender='user'
")->fetch_assoc()['total'];

$chat_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Montserrat', sans-serif; background: linear-gradient(135deg, #fbd3e9, #bb377d); margin:0; }
header { background: #fff; padding:20px 40px; border-bottom-left-radius:15px; border-bottom-right-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.1); display:flex; justify-content:space-between; align-items:center;}
header h1 { margin:0; color:#a855f7; font-weight:800; text-transform:uppercase; letter-spacing:1px; font-size:28px; }
nav a { margin-left:10px; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; color:#fff; transition: all 0.3s ease; }
nav a.main { background:#34d399; }       
nav a.logout { background:#f472b6; }     
.container { width:90%; max-width:1200px; margin:30px auto; }
h2 { color:#6b21a8; font-weight:700; margin-bottom:15px; }
.stats { display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap; }
.stat-box { flex:1; background: linear-gradient(135deg,#f472b6,#ec4899); color:#fff; padding:25px; border-radius:12px; text-align:center; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
.stat-box h3 { margin:0; font-size:26px; }
.stat-box p { margin:5px 0 0; font-size:16px; }
table { width:100%; border-collapse:collapse; margin-bottom:30px; border-radius:12px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
th, td { padding:12px; text-align:center; }
th { background:#a855f7; color:#fff; }
tr:nth-child(even) { background:#f9e6f6; }
tr:nth-child(odd) { background:#fff; color:#6b21a8; }
button, .btn-action { padding:8px 12px; border:none; border-radius:8px; cursor:pointer; font-weight:600; transition:0.3s; color:#fff; text-decoration:none; }
button:hover, .btn-action:hover { transform:translateY(-2px); opacity:0.9; }
.btn-add { background: linear-gradient(90deg,#f472b6,#ec4899); }
.btn-edit { background:#34d399; }
.btn-edit:hover { background:#10b981; }
.btn-delete { background:#f87171; }
.btn-delete:hover { background:#dc2626; }
img { max-width:60px; border-radius:6px; }
.new-admin { background:rgba(255,255,255,0.95); padding:20px; border-radius:15px; color:#6b21a8; margin-bottom:30px;}
.new-admin input { padding:10px; margin:5px 0; border-radius:8px; border:1px solid #d1d5db; width:100%; }
.new-admin button { margin-top:10px; }
.new-admin p { margin-top:10px; font-weight:600; color:#ec4899; }
@media (max-width:768px) { .stats { flex-direction:column; } table { font-size:14px; } }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA Admin Dashboard</h1>
    <nav>
        <a href="../index.php" class="main">Go to Main Site</a>
        <a href="?logout" class="logout">Logout</a>
    </nav>
</header>

<div class="container">

    <!-- Stats -->
    <h2>Overview</h2>
    <div class="stats">
        <div class="stat-box">
            <h3><?= $totalUsers ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-box">
            <h3><?= $totalOrders ?></h3>
            <p>Total Orders</p>
        </div>
    </div>

    <!-- Add New Admin -->
    <h2>Add New Admin</h2>
    <div class="new-admin">
        <form method="POST">
            <input type="text" name="username" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_admin" class="btn-action btn-add">Add Admin</button>
        </form>
        <?php if($admin_msg): ?><p><?= htmlspecialchars($admin_msg) ?></p><?php endif; ?>
    </div>

    <!-- Current Admins -->
    <h2>Current Admins</h2>
    <?php if($admins && $admins->num_rows > 0): ?>
    <table>
        <tr style="background:#a855f7;color:#fff;">
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Created At</th>
        </tr>
        <?php while($admin = $admins->fetch_assoc()): ?>
        <tr style="background:#fff;">
            <td><?= $admin['id'] ?></td>
            <td><?= htmlspecialchars($admin['full_name']) ?></td>
            <td><?= htmlspecialchars($admin['email']) ?></td>
            <td><?= $admin['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <p style="color:#fff;font-weight:600;">No admins found.</p>
    <?php endif; ?>

    <!-- Latest Orders -->
    <h2>Latest Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User Name</th>
            <th>User Email</th>
            <th>Gender</th>
            <th>Payment</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while($order = $latestOrdersQuery->fetch_assoc()): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td><?= htmlspecialchars($order['email']) ?></td>
            <td><?= htmlspecialchars($order['gender']) ?></td>
            <td><?= htmlspecialchars($order['payment_method']) ?></td>
            <td>$<?= number_format($order['total_price'],2) ?></td>
            <td><?= ucfirst($order['status']) ?></td>
            <td><?= $order['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Latest Order Details -->
    <?php if($latestOrder): ?>
    <h2>Latest Order Details</h2>
    <div style="background:rgba(255,255,255,0.95); padding:20px; border-radius:15px; color:#6b21a8; margin-bottom:30px;">
        <p><strong>Order ID:</strong> <?= $latestOrder['order_id'] ?></p>
        <p><strong>User Name:</strong> <?= htmlspecialchars($latestOrder['user_name']) ?></p>
        <p><strong>User Email:</strong> <?= htmlspecialchars($latestOrder['email']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($latestOrder['payment_method']) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($latestOrder['status']) ?></p>
        <p><strong>Total:</strong> $<?= number_format($latestOrder['total_price'],2) ?></p>
        <p><strong>Date:</strong> <?= $latestOrder['created_at'] ?></p>

        <h3>Order Items</h3>
        <table>
            <tr style="background:#a855f7; color:#fff;">
                <th>Image</th>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($orderItems as $item):
                $subtotal = $item['price'] * $item['quantity'];
            ?>
            <tr style="background:#f9e6f6; color:#6b21a8;">
                <td><img src="../assets/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= ucfirst($item['category']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($item['price'],2) ?></td>
                <td>$<?= number_format($subtotal,2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- Orders Per User -->
    <h2>Orders Per User</h2>
    <table>
        <tr>
            <th>User Name</th>
            <th>User Email</th>
            <th>Number of Orders</th>
        </tr>
        <?php while($user = $ordersPerUserQuery->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['orders_count'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Products -->
    <h2>Products</h2>
    <a href="add_product.php" class="btn-action btn-add">Add Product</a>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Size</th>
            <th>Color</th>
            <th>Actions</th>
        </tr>
        <?php while($p = $products->fetch_assoc()): ?>

        
        <tr>
            <td><img src="../assets/images/<?= $p['image'] ?>" alt=""></td>
            <td><img src="../<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="max-width:60px;"></td>

            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= ucfirst($p['category']) ?></td>
            <td>$<?= number_format($p['price'],2) ?></td>
            <td><?= htmlspecialchars($p['size']) ?></td>
            <td><?= htmlspecialchars($p['color']) ?></td>
            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-action btn-edit">Edit</a>
                <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" class="btn-action btn-delete">Delete</a>

            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_support.php">
    Messages
    <?php if($unread > 0): ?>
        <span style="
            background:#ec4899;
            color:#fff;
            padding:4px 8px;
            border-radius:50%;
            font-size:12px;">
            <?= $unread ?>
        </span>
    <?php endif; ?>
    </a>

</div>

<div id="adminChat" style="background:#fff;padding:15px;border-radius:12px;max-height:300px;overflow-y:auto;"></div>
<script>
const USER_ID = <?= $chat_user_id ?>;

function loadAdminChat(uid){
    if(uid === 0) return;

    fetch("admin_fetch_messages.php?user_id=" + uid)
        .then(r => r.text())
        .then(d => {
            document.getElementById("adminChat").innerHTML = d;
        });
}

setInterval(() => loadAdminChat(USER_ID), 2000);
loadAdminChat(USER_ID);
</script>

</body>
</html>
