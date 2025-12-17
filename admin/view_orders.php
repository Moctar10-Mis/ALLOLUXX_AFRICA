<?php
session_start();
include '../php/config.php';

// Redirect if not logged in as admin
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Check if order_id is provided
if(!isset($_GET['order_id'])){
    header("Location: admin_dashboard.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Handle status update
if(isset($_POST['update_status'])){
    $new_status = $_POST['status'];
    $update_query = "UPDATE orders SET status='$new_status' WHERE id=$order_id";
    mysqli_query($conn, $update_query);
    $_SESSION['status_msg'] = "Order status updated successfully!";
    header("Location: view_order.php?order_id=$order_id");
    exit();
}

// Fetch order info
$order_query = "
    SELECT o.id as order_id, o.user_id, o.total_price, o.payment_method, o.status, o.created_at,
           u.full_name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = $order_id
";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$items_query = "
    SELECT oi.id, p.name, p.image, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order - Admin | ALOLLUXX AFRICA</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA - Admin View Order</h1>
        <nav>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>

<section class="admin-orders">
    <?php if(isset($_SESSION['status_msg'])): ?>
        <p style="color:green;text-align:center;"><?php echo $_SESSION['status_msg']; unset($_SESSION['status_msg']); ?></p>
    <?php endif; ?>

    <h2>Order #<?php echo $order['order_id']; ?> Details</h2>
    <p><strong>User:</strong> <?php echo $order['full_name']; ?> | <strong>Email:</strong> <?php echo $order['email']; ?></p>
    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?> | 
       <strong>Payment Method:</strong> <?php echo $order['payment_method']; ?> | 
       <strong>Status:</strong> <?php echo $order['status']; ?> | 
       <strong>Ordered At:</strong> <?php echo $order['created_at']; ?></p>

    <form method="POST" style="margin-bottom:20px;">
        <label for="status">Update Status:</label>
        <select name="status" id="status">
            <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
            <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
        </select>
        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
    </form>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = mysqli_fetch_assoc($items_result)): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><img src="../assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" width="80"></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-card"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</section>

</body>
</html>
