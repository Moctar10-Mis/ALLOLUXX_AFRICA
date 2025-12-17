<?php
session_start();
include('php/config.php');

// Redirect if not logged in or wrong gender
if (!isset($_SESSION['user_id']) || $_SESSION['gender'] != 'woman') {
    header("Location: login.php");
    exit();
}

// Make sure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Compute cart count
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantity'];
}

// Handle Add to Cart from this dashboard
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);

    $q = $conn->prepare("SELECT * FROM products WHERE id=?");
    $q->bind_param("i", $id);
    $q->execute();
    $p = $q->get_result()->fetch_assoc();

    if ($p) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'price' => $p['price'],
                'quantity' => 1,
                'image' => $p['image']
            ];
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Man Dashboard - ALOLLUXX AFRICA</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
// Disable browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // prevents going back
};
</script>
</head>

<body>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>

<section class="dashboard-hero man-hero">
    <h2>Welcome <?php echo $_SESSION['full_name']; ?>!!</h2>
    <div class="dash-image">
        <img src="assets/images/women/image3.jpeg" alt="Dashboard Photo">
    </div>
    <p>Manage your orders, cart, checkout, and payments.</p>
</section>

<section class="dashboard-products">
    <h2>Available Products</h2>
    <div class="products-container">

        <?php
        $category = 'woman'; // MUST MATCH DATABASE
        $query = "SELECT * FROM products WHERE category='$category'";
        $result = mysqli_query($conn, $query);

        while ($product = mysqli_fetch_assoc($result)):
        ?>

        <div class="product-card">
            <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p>$<?php echo number_format($product['price'], 2); ?></p>

            <div class="product-buttons">
                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-details">View Details</a>
                <a href="?add=<?php echo $product['id']; ?>" class="btn btn-cart">Add to Cart</a>
            </div>
        </div>

        <?php endwhile; ?>

    </div>
</section>

<section class="dashboard-cards">
    <div class="cards">

        <!-- CART -->
        <div class="card">
            <i class="fas fa-shopping-cart fa-3x"></i>
            <h3>Cart</h3>
            <p>View items you have added to your cart.</p>
            <a href="cart.php" class="btn-card">
                Go to Cart (<?php echo $cartCount; ?>)
            </a>
        </div>

        <!-- ORDERS -->
        <div class="card">
            <i class="fas fa-box fa-3x"></i>
            <h3>Orders</h3>
            <p>Check your previous orders.</p>
            <a href="orders.php" class="btn-card">View Orders</a>
        </div>

        <!-- PAYMENT -->
        <div class="card">
            <i class="fas fa-credit-card fa-3x"></i>
            <h3>Payment</h3>
            <p>Complete your purchase securely.</p>
            <a href="checkout.php" class="btn-card">Proceed to Payment</a>
        </div>

        <!-- PURCHASES -->
        <div class="card">
            <i class="fas fa-credit-card fa-3x"></i>
            <h3>Purchases</h3>
            <p>See your completed purchases.</p>
            <a href="purchases.php" class="btn-card">View Purchases</a>
        </div>

    </div>
</section>

<footer>
    <p>&copy; 2025 ALOLLUXX AFRICA. All rights reserved.</p>
</footer>
</body>
</html>
