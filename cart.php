<?php
session_start();
include('php/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART HANDLER
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['image']
            ];
        }
    }

    header("Location: cart.php");
    exit();
}

// REMOVE ITEM
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>
</header>

<h2>Your Cart</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Your cart is empty.</p>
<?php else: ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php foreach ($_SESSION['cart'] as $item): 
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><?php echo $item['name']; ?></td>
        <td>$<?php echo number_format($item['price'], 2); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>$<?php echo number_format($subtotal, 2); ?></td>
        <td>
            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger">
                Remove
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>

<a href="checkout.php" class="btn btn-primary">Proceed to Payment</a>

<?php endif; ?>

<!-- GO BACK -->
<div class="bottom-actions">
    <a href="man_dashboard.php" class="btn btn-back">Back to Dashboard</a>
</div>

<!-- BLOCK BROWSER BACK -->
<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>

</body>
</html>
