<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - ALOLLUXX AFRICA</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- HEADER -->
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav class="top-actions">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </nav>
    </div>
</header>

<!-- ORDERS SECTION -->
<section class="orders-section">
    <h2>My Orders (Not Paid)</h2>

    <?php if (empty($cart)): ?>
        <p>No pending orders.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Date</th>
            </tr>

            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td><?php echo date("Y-m-d"); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>

<!-- BACK BUTTON -->
<div class="bottom-actions">
    <a href="man_dashboard.php" class="btn btn-back">Go Back</a>
</div>

</body>
</html>
