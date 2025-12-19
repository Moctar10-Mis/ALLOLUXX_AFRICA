<?php
session_start();
include('php/config.php');

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
 Fetch orders + order items + product images
*/
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.total_price,
        o.status,
        o.created_at,
        p.name AS product_name,
        p.image AS product_image,
        oi.quantity,
        oi.price
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

/*
 Group data by order_id
*/
$orders = [];
while ($row = $result->fetch_assoc()) {
    $oid = $row['order_id'];

    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'created_at' => $row['created_at'],
            'total_price' => $row['total_price'],
            'status' => $row['status'],
            'items' => []
        ];
    }

    $orders[$oid]['items'][] = [
        'name' => $row['product_name'],
        'image' => $row['product_image'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Montserrat', sans-serif;
    margin:0;
    padding:0;
    background: linear-gradient(135deg,#fbd3e9,#bb377d);
    color:#6b21a8;
}
header {
    background:#fff;
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
header h1 { margin:0; color:#a855f7; }
.container {
    width:90%;
    max-width:1000px;
    margin:30px auto;
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}
.order-box {
    margin-bottom:30px;
    padding:20px;
    border-radius:12px;
    background:#fdf4ff;
}
.order-header {
    display:flex;
    justify-content:space-between;
    margin-bottom:15px;
    font-weight:600;
}
table {
    width:100%;
    border-collapse:collapse;
}
th, td {
    padding:12px;
    text-align:center;
}
th {
    background:#f3e8ff;
}
img {
    width:60px;
    border-radius:8px;
    box-shadow:0 4px 8px rgba(0,0,0,0.1);
}
.btn {
    display:inline-block;
    padding:10px 20px;
    background:#9ca3af;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
}
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <a href="logout.php" class="btn">Logout</a>
</header>

<div class="container">
    <h2 style="text-align:center;">My Orders</h2>

    <?php if (empty($orders)): ?>
        <p style="text-align:center;">You have no orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order_id => $order): ?>
            <div class="order-box">
                <div class="order-header">
                    <span>Order #<?= $order_id ?></span>
                    <span><?= date("Y-m-d", strtotime($order['created_at'])) ?></span>
                    <span>Status: <?= htmlspecialchars($order['status']) ?></span>
                </div>

                <table>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>

                    <?php foreach ($order['items'] as $item):
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="">
                        </td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'],2) ?></td>
                        <td>$<?= number_format($subtotal,2) ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>$<?= number_format($order['total_price'],2) ?></strong></td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="man_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
