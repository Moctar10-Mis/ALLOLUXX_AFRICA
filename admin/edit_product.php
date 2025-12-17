<?php
session_start();
include '../php/config.php';

//Prevent caching to disable browser back button
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

// Check admin session
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

// Delete product
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: edit_product.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Products - Admin</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA Admin</h1>
        <nav>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            <a href="../index.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>

<section class="dashboard-products">
    <h2>Edit / Delete Products</h2>
    <div class="products-container">
        <?php while($product = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="../assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            <h3><?php echo $product['name']; ?></h3>
            <p>$<?php echo number_format($product['price'],2); ?></p>
            <div class="product-buttons">
                <a href="edit_single_product.php?id=<?php echo $product['id']; ?>" class="btn btn-details">Edit</a>
                <a href="edit_product.php?delete=<?php echo $product['id']; ?>" class="btn btn-cart" onclick="return confirm('Delete this product?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
</body>
</html>
