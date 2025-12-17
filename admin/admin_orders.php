<?php
session_start();
include 'php/config.php';

// Prevent caching to disable browser back button
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login/index if not logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Logout action
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Make sure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Delete order items first
    $stmtItemsDel = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmtItemsDel->bind_param("i", $delete_id);
    $stmtItemsDel->execute();

    // Delete order
    $stmtOrderDel = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmtOrderDel->bind_param("i", $delete_id);
    $stmtOrderDel->execute();

    header("Location: admin_orders.php");
    exit();
}

// Fetch all orders
$orderQuery = $conn->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$orderQuery->execute();
$ordersResult = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - All Orders</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background-color: #f2f2f2; }
.btn { padding: 5px 10px; background: #007BFF; color: #fff; text-decoration: none; border-radius: 4px; }
.btn:hover { background: #0056b3; }
.btn-delete { background-color: #dc3545; margin-left: 10px; }
.btn-delete:hover { background-color: #a71d2a; }
</style>
</head>
<body>

<h1>Admin Panel - All Orders</h1>

<?php if ($ordersResult->num_rows == 0): ?>
    <p>No orders found.</p>
<?php else: ?>
    <?php while ($order = $ordersResult->fetch_assoc()): ?>
        <h3>
            Order #<?php echo $order['id']; ?> | User: <?php echo htmlspecialchars($order['username']); ?> | 
            Date: <?php echo $order['created_at']; ?> | Method: <?php echo $order['payment_method']; ?> | Status: <?php echo $order['status']; ?>
            <a href="admin_orders.php?delete=<?php echo $order['id']; ?>" onclick="return confirm('Delete this order?');" class="btn btn-delete">Delete</a>
        </h3>

        <?php
        // Fetch order items
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
            <?php while ($item = $itemsResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php $order_total += $item['price'] * $item['quantity']; ?>
            <?php endwhile; ?>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong>$<?php echo number_format($order_total, 2); ?></strong></td>
            </tr>
        </table>
    <?php endwhile; ?>
<?php endif; ?>

<a href="admin_dashboard.php" class="btn">Back to Dashboard</a>

</body>
</html>
