<?php
session_start();
include '../php/config.php';

// Protect page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Get order_id from GET to show the form
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: admin_dashboard.php");
    exit();
}

$orderResult = $conn->prepare("SELECT id, status FROM orders WHERE id = ?");
$orderResult->bind_param("i", $order_id);
$orderResult->execute();
$result = $orderResult->get_result();
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Order Status</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background-color: #f4f6f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 400px;
    text-align: center;
}
h1 {
    color: #007BFF;
    margin-bottom: 20px;
}
select, button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}
button {
    background-color: #007BFF;
    color: white;
    border: none;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<div class="container">
    <h1>Update Order Status</h1>

    <form method="POST" action="">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

        <label for="status">Select Status:</label>
        <select name="status" id="status" required>
            <option value="Pending" <?= $order['status']=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Paid" <?= $order['status']=='Paid'?'selected':'' ?>>Paid</option>
            <option value="Shipped" <?= $order['status']=='Shipped'?'selected':'' ?>>Shipped</option>
            <option value="Delivered" <?= $order['status']=='Delivered'?'selected':'' ?>>Delivered</option>
            <option value="Cancelled" <?= $order['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
        </select>

        <button type="submit" name="update_status">Update Status</button>
    </form>

    <a href="admin_dashboard.php" style="display:block;margin-top:15px;color:#007BFF;text-decoration:none;">⬅ Back to Dashboard</a>
</div>

</body>
</html>
