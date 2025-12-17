<?php
session_start();
include 'php/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get cart from session
$cart = $_SESSION['cart'] ?? [];

// If cart is empty
if (empty($cart)) {
    die("<p>Your cart is empty. <a href='cart.php'>Go back to cart</a></p>");
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle payment
if (isset($_POST['pay'])) {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $status = 'Paid';

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $total, $payment_method, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmtItem->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmtItem->execute();
    }

    // Clear cart
    $_SESSION['cart'] = [];

    // Success message and redirect
    $_SESSION['order_success'] = "Thank you! Your order was successful.";
    header("Location: thankyou.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - ALOLLUXX AFRICA</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background-color: #f2f2f2; }
.btn { padding: 10px 20px; background: #007BFF; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
.btn:hover { background: #0056b3; }
</style>
</head>
<body>

<h2>Checkout</h2>

<table>
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
</tr>
<?php foreach ($cart as $item): ?>
<tr>
    <td><?php echo htmlspecialchars($item['name']); ?></td>
    <td>$<?php echo number_format($item['price'], 2); ?></td>
    <td><?php echo $item['quantity']; ?></td>
    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="3"><strong>Total</strong></td>
    <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
</tr>
</table>

<form method="POST" action="checkout.php">
    <h3>Select Payment Method</h3>
    <label><input type="radio" name="payment_method" value="MoMo" required> MoMo</label><br>
    <label><input type="radio" name="payment_method" value="Card"> Card</label><br>
    <label><input type="radio" name="payment_method" value="COD"> Cash on Delivery</label><br><br>

    <button type="submit" name="pay" class="btn">Pay & Place Order</button>
</form>

<a href="cart.php" class="btn" style="margin-top:10px; display:inline-block;">Back to Cart</a>

</body>
</html>
