<?php
session_start();
include('php/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this user that are not paid
$stmt = $conn->prepare("SELECT o.id AS order_id, o.total_price, o.status, o.created_at, oi.product_id, p.name, oi.quantity, oi.price 
                        FROM orders o 
                        JOIN order_items oi ON o.id = oi.order_id 
                        JOIN products p ON oi.product_id = p.id
                        WHERE o.user_id = ? AND o.status != 'Paid'
                        ORDER BY o.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Organize orders by order_id
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['created_at'] = $row['created_at'];
    $orders[$row['order_id']]['total_price'] = $row['total_price'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['items'][] = [
        'name' => $row['name'],
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
body { font-family:'Montserrat',sans-serif; margin:0; padding:0; background:linear-gradient(135deg,#fbd3e9,#bb377d); color:#6b21a8; }
header { background:#fff; padding:20px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
header h1 { margin:0; color:#a855f7; }
.container { width:90%; max-width:900px; margin:30px auto; padding:20px; border-radius:12px; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,.1);}
h2 { text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#f3e8ff; color:#6b21a8; }
.btn { padding:10px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; margin:5px 0; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
.btn-back { background:#9ca3af; color:#fff; }
.order-box { margin-bottom:30px; padding:15px; border-radius:10px; background:#fdf4ff; box-shadow:0 4px 12px rgba(0,0,0,0.05);}
.order-header { display:flex; justify-content:space-between; margin-bottom:10px; }
.order-items td { text-align:left; }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <a href="logout.php" class="btn btn-back">Logout</a>
</header>

<div class="container">
    <h2>My Orders (Not Paid)</h2>

    <?php if (empty($orders)): ?>
        <p style="text-align:center;">No pending orders.</p>
    <?php else: ?>
        <?php foreach ($orders as $order_id => $order): ?>
            <div class="order-box">
                <div class="order-header">
                    <span><strong>Order #<?= $order_id ?></strong></span>
                    <span>Date: <?= date("Y-m-d", strtotime($order['created_at'])) ?></span>
                    <span>Status: <?= htmlspecialchars($order['status']) ?></span>
                </div>
                <table class="order-items">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($order['items'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'],2) ?></td>
                        <td>$<?= number_format($subtotal,2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>$<?= number_format($order['total_price'],2) ?></strong></td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="text-align:center; margin-top:20px;">
        <a href="man_dashboard.php" class="btn btn-back">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
