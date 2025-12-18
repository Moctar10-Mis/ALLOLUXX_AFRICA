<?php
session_start();
include('php/config.php');

// Make sure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Compute total
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart - ALLOLUX AFRICA</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.cart-container { width:90%; max-width:900px; margin:40px auto; }
.cart-item { display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #ccc; }
.cart-item img { width:100px; height:100px; object-fit:cover; border-radius:8px; margin-right:20px; }
.item-details { flex:1; }
.item-actions { text-align:right; }
.item-actions a { margin-left:10px; text-decoration:none; color:#a855f7; font-weight:600; }
.total { text-align:right; font-size:24px; font-weight:700; margin-top:20px; color:#6b21a8; }
.btn-checkout { display:inline-block; margin-top:20px; padding:12px 25px; background:#f472b6; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <nav>
        <a href="<?= ($_SESSION['gender'] ?? '') === 'man' ? 'man_dashboard.php' : 'woman_dashboard.php'; ?>" class="btn">Dashboard</a>
        <a href="logout.php" class="btn">Logout</a>
    </nav>
</header>

<div class="cart-container">
    <h2>Your Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                <div class="item-details">
                    <h3><?= htmlspecialchars($item['name']); ?></h3>
                    <p>Price: $<?= number_format($item['price'], 2); ?></p>
                    <p>Quantity: <?= $item['quantity']; ?></p>
                    <p>Subtotal: $<?= number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
                <div class="item-actions">
                    <a href="cart.php?remove=<?= $item['id']; ?>">Remove</a>
                    <a href="cart.php?add=<?= $item['id']; ?>">+</a>
                    <a href="cart.php?decrease=<?= $item['id']; ?>">-</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total">Total: $<?= number_format($totalPrice, 2); ?></div>
        <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
    <?php endif; ?>
</div>

</body>
</html>
