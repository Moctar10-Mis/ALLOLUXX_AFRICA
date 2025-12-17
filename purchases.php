<?php
session_start();
include ('php/config.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle deletion
if(isset($_GET['delete'])){
    $delete_id = intval($_GET['delete']);

    // Delete order items first
    $stmtItemsDel = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmtItemsDel->bind_param("i", $delete_id);
    $stmtItemsDel->execute();

    // Delete order
    $stmtOrderDel = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmtOrderDel->bind_param("ii", $delete_id, $user_id);
    $stmtOrderDel->execute();

    header("Location: purchases.php");
    exit();
}

// Fetch all orders for this user
$orderQuery = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderQuery->bind_param("i", $user_id);
$orderQuery->execute();
$ordersResult = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Purchases - ALOLLUXX AFRICA</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 20px; background: #007BFF; color: #fff; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
        .btn-delete { background-color: #dc3545; color: #fff; padding: 5px 10px; text-decoration: none; border-radius: 4px; margin-left: 10px; }
        .btn-delete:hover { background-color: #a71d2a; }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav class="top-actions">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </nav>
    </div>
</header>

<h2>My Purchases</h2>

<?php if($ordersResult->num_rows == 0): ?>
    <p>You have not made any purchases yet.</p>
<?php else: ?>
    <?php while($order = $ordersResult->fetch_assoc()): ?>
        <h3>
            Order #<?php echo $order['id']; ?> | <?php echo $order['created_at']; ?> | 
            Method: <?php echo $order['payment_method']; ?> | Status: <?php echo $order['status']; ?>
            <a href="purchases.php?delete=<?php echo $order['id']; ?>" 
               onclick="return confirm('Are you sure you want to delete this purchase?');" 
               class="btn-delete">Delete</a>
        </h3>
        
        <?php
        // Fetch items for this order
        $stmtItems = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmtItems->bind_param("i", $order['id']);
        $stmtItems->execute();
        $itemsResult = $stmtItems->get_result();
        ?>

        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php $order_total = 0; ?>
            <?php while($item = $itemsResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'],2); ?></td>
                    <td>$<?php echo number_format($item['price']*$item['quantity'],2); ?></td>
                </tr>
                <?php $order_total += $item['price']*$item['quantity']; ?>
            <?php endwhile; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong>$<?php echo number_format($order_total,2); ?></strong></td>
            </tr>
        </table>
    <?php endwhile; ?>
<?php endif; ?>

<div class="bottom-actions">
    <a href="man_dashboard.php" class="btn">Back to Dashboard</a>
</div>
</body>
</html>
