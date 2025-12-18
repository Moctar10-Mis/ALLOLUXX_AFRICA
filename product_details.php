<?php
session_start();
include('php/config.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("<p>Product not found. <a href='index.php'>Go back</a></p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']); ?> - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Montserrat', sans-serif;
    margin:0;
    padding:0;
    background: linear-gradient(135deg,#fbd3e9,#bb377d);
    color: #6b21a8;
}
header {
    background:#fff;
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}
header h1 { margin:0; color:#a855f7; }
header .btn { padding:10px 18px; background:#f472b6; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; }
.container { width:90%; max-width:900px; margin:30px auto; background:#fff; border-radius:12px; padding:30px; box-shadow:0 8px 20px rgba(0,0,0,0.1); display:flex; gap:30px; flex-wrap:wrap; }
.product-image { flex:1; min-width:300px; }
.product-image img { width:100%; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.product-info { flex:1; min-width:300px; }
.product-info h2 { font-size:32px; margin-bottom:10px; }
.product-info .price { font-size:24px; font-weight:700; margin-bottom:15px; color:#a855f7; }
.product-info .description { margin-bottom:20px; }
.btn-primary { background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; padding:12px 25px; border-radius:8px; font-weight:600; text-decoration:none; display:inline-block; }
.btn-primary:hover { opacity:0.9; }
.back-link { display:inline-block; margin-top:20px; text-decoration:none; color:#6b21a8; font-weight:600; }
.back-link:hover { text-decoration:underline; }
</style>
<script>
// Optional: disable browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () { history.go(1); };
</script>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <div>
        <a href="cart.php" class="btn">Cart</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</header>

<div class="container">
    <div class="product-image">
        <img src="assets/images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-info">
        <h2><?= htmlspecialchars($product['name']); ?></h2>
        <p class="price">$<?= number_format($product['price'], 2); ?></p>
        <p class="description"><?= htmlspecialchars($product['description']); ?></p>

        <a href="cart.php?add=<?= $product['id']; ?>" class="btn-primary">Add to Cart</a>

        <br><br>

        <a href="<?= ($_SESSION['gender'] ?? '') === 'man' ? 'man_dashboard.php' : 'woman_dashboard.php'; ?>" class="back-link"> Back to Dashboard</a>
    </div>
</div>

</body>
</html>
