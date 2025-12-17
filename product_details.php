<?php
session_start();
include ('php/config.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM products WHERE id=$id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $product['name']; ?> - ALOLLUXX AFRICA</title>
<link rel="stylesheet" href="assets/css/style.css">
<script>
// Disable browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1); // user cannot go back
};
</script>
</head>

<body>

<header class="product-header">
    <h1>ALOLLUXX AFRICA</h1>
    <a href="cart.php" class="btn">Cart</a>
        <nav class="top-actions">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </nav>
    </div>


</header>

<section class="product-details">
    <div class="product-image">
        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    </div>

    <div class="product-info">
        <h2><?php echo $product['name']; ?></h2>
        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
        <p class="description"><?php echo $product['description']; ?></p>

        <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary">
            Add to Cart
        </a>

        <br><br>

        <a href="<?php echo ($_SESSION['gender'] == 'man') ? 'man_dashboard.php' : 'woman_dashboard.php'; ?>">
            ← Back to Dashboard
        </a>
    </div>
</section>

</body>
</html>