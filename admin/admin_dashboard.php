<?php
session_start();
include '../php/config.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect if not logged in
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

// Fetch total users
$totalUsersResult = $conn->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];

// Fetch total orders
$totalOrdersResult = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];

// Fetch latest 10 orders
$latestOrdersQuery = $conn->query("
    SELECT o.id AS order_id, o.total_price, o.status, o.created_at, o.payment_method,
           u.email, u.gender, u.full_name AS user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Fetch latest single order
$latestOrderResult = $conn->query("
    SELECT o.id AS order_id, o.total_price, o.status, o.payment_method, o.created_at,
           u.full_name AS user_name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 1
");
$latestOrder = $latestOrderResult->fetch_assoc();

// Fetch items for the latest order
$orderItems = [];
if ($latestOrder) {
    $orderId = $latestOrder['order_id'];
    $itemsResult = $conn->query("
        SELECT p.name, p.image, p.category, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $orderId
    ");
    while ($row = $itemsResult->fetch_assoc()) {
        $orderItems[] = $row;
    }
}

// Orders per user
$ordersPerUserQuery = $conn->query("
    SELECT u.full_name AS user_name, u.email, COUNT(o.id) AS orders_count
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY orders_count DESC
");

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - ALOLUXX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Montserrat', sans-serif; background:#f4f6f8; margin:0; }
header { background:#007BFF; color:#fff; padding:20px; text-align:center; }
header h1 { margin:0; font-size:28px; }
nav { text-align:right; margin-top:-35px; margin-right:20px; }
nav a { color:#fff; text-decoration:none; background:#0056b3; padding:8px 15px; border-radius:5px; margin-left:5px; }
nav a:hover { background:#003f7f; }
.container { width:90%; max-width:1200px; margin:20px auto; }
h2 { color:#333; }
.stats { display:flex; gap:20px; margin-bottom:30px; }
.stat-box { background:#007BFF; color:#fff; flex:1; padding:20px; border-radius:8px; text-align:center; }
.stat-box h3 { margin:0; font-size:24px; }
.stat-box p { margin:5px 0 0; font-size:16px; }
table { width:100%; border-collapse:collapse; margin-bottom:30px; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; }
th { background-color:#007BFF; color:#fff; }
tr:nth-child(even) { background-color:#f2f2f2; }
button { padding:8px 12px; border:none; border-radius:5px; background:#28a745; color:#fff; cursor:pointer; }
button:hover { background:#218838; }
img { max-width:60px; }
</style>
</head>
<body>

<header>
    <h1>ALOLLUXX AFRICA Admin Dashboard</h1>
    <nav>
        <a href="../index.php">Go to Main Site</a>
        <a href="../index.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Overview</h2>
    <div class="stats">
        <div class="stat-box">
            <h3><?php echo $totalUsers; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $totalOrders; ?></h3>
            <p>Total Orders</p>
        </div>
    </div>

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
            <td><?php echo $order['order_id']; ?></td>
            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
            <td><?php echo htmlspecialchars($order['email']); ?></td>
            <td><?php echo htmlspecialchars($order['gender']); ?></td>
            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
            <td>$<?php echo number_format($order['total_price'],2); ?></td>
            <td><?php echo ucfirst($order['status']); ?></td>
            <td><?php echo $order['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Orders Per User</h2>
    <table>
        <tr>
            <th>User Name</th>
            <th>User Email</th>
            <th>Number of Orders</th>
        </tr>
        <?php while($user = $ordersPerUserQuery->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['orders_count']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Products</h2>
    <a href="add_product.php"><button>Add Product</button></a>
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
            <td><img src="../assets/images/<?php echo $p['image']; ?>" alt=""></td>
            <td><?php echo $p['name']; ?></td>
            <td><?php echo ucfirst($p['category']); ?></td>
            <td>$<?php echo number_format($p['price'],2); ?></td>
            <td><?php echo $p['size']; ?></td>
            <td><?php echo $p['color']; ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $p['id']; ?>">✏ Edit</a> |
                <a href="delete_product.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?')">🗑 Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if($latestOrder): ?>
    <h2>Latest Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo $latestOrder['order_id']; ?></p>
    <p><strong>User Name:</strong> <?php echo $latestOrder['user_name']; ?></p>
    <p><strong>User Email:</strong> <?php echo $latestOrder['email']; ?></p>
    <p><strong>Payment:</strong> <?php echo $latestOrder['payment_method']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($latestOrder['status']); ?></p>
    <p><strong>Total:</strong> $<?php echo number_format($latestOrder['total_price'],2); ?></p>
    <p><strong>Date:</strong> <?php echo $latestOrder['created_at']; ?></p>

    <table>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Category</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach($orderItems as $item): 
            $folder = ($item['category'] === 'man') ? 'men' : 'women';
            $imgPath = "../assets/images/$folder/".$item['image'];
            $subtotal = $item['price'] * $item['quantity'];
        ?>
        <tr>
            <td><img src="<?php echo $imgPath; ?>" alt=""></td>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo ucfirst($item['category']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>$<?php echo number_format($item['price'],2); ?></td>
            <td>$<?php echo number_format($subtotal,2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
