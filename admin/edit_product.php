<?php
session_start();
include '../php/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

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
<title>Edit Products - ALLOLUX AFRICA</title>
<style>
body { font-family: "Segoe UI", Tahoma, sans-serif; margin:0; padding:0; background:linear-gradient(135deg,#f9a8d4,#ec4899,#be185d); min-height:100vh; }
header { text-align:center; padding:40px 0; color:#fff; font-size:36px; font-weight:900; letter-spacing:3px; text-transform:uppercase; text-shadow:2px 2px 8px rgba(0,0,0,0.3); }
header nav { margin-top:15px; }
header nav a { display:inline-block; margin:0 10px; padding:12px 20px; background:linear-gradient(90deg,#f9a8d4,#ec4899,#be185d); color:#fff; text-decoration:none; font-weight:700; border-radius:12px; transition: all 0.3s ease;}
header nav a:hover { background:linear-gradient(90deg,#ec4899,#be185d,#9d174d); transform:translateY(-2px);}
.dashboard-products { max-width:1200px; margin:40px auto; padding:0 20px; }
.dashboard-products h2 { text-align:center; color:#fff; font-size:32px; font-weight:800; margin-bottom:40px; text-shadow:1px 1px 6px rgba(0,0,0,0.3);}
.products-container { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; }
.product-card { background:rgba(255,255,255,0.95); border-radius:20px; padding:20px; width:220px; text-align:center; box-shadow:0 15px 35px rgba(0,0,0,0.25); transition: transform 0.3s ease, box-shadow 0.3s ease;}
.product-card:hover { transform:translateY(-5px); box-shadow:0 20px 50px rgba(0,0,0,0.35);}
.product-card img { width:100%; border-radius:15px; border:2px solid #f9a8d4; margin-bottom:15px; transition: transform 0.3s ease, box-shadow 0.3s ease;}
.product-card img:hover { transform:scale(1.05); box-shadow:0 10px 25px rgba(0,0,0,0.35);}
.product-card h3 { font-size:20px; color:#be185d; margin-bottom:10px; font-weight:700;}
.product-card p { font-size:16px; color:#334155; margin-bottom:15px; }
.product-buttons { display:flex; gap:10px; justify-content:center; }
.product-buttons a { flex:1; text-decoration:none; padding:10px 0; border-radius:12px; font-weight:600; color:#fff; transition: all 0.3s ease; }
.btn-details { background:linear-gradient(90deg,#f9a8d4,#ec4899,#be185d);}
.btn-details:hover { background:linear-gradient(90deg,#ec4899,#be185d,#9d174d); transform:translateY(-2px);}
.btn-cart { background:linear-gradient(90deg,#f472b6,#d946ef,#9333ea);}
.btn-cart:hover { background:linear-gradient(90deg,#d946ef,#9333ea,#7e22ce); transform:translateY(-2px);}
@media (max-width:768px) { .products-container { flex-direction:column; align-items:center;} .product-card { width:90%;} header nav a { margin:10px 5px; width:90%;} }
</style>
</head>
<body>
<header>
ALLOLUX AFRICA
<nav>
<a href="admin_dashboard.php">Dashboard</a>
<a href="../index.php">Logout</a>
</nav>
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
<a href="edit_single_product.php?id=<?php echo $product['id']; ?>" class="btn-details">Edit</a>
<a href="edit_product.php?delete=<?php echo $product['id']; ?>" class="btn-cart" onclick="return confirm('Delete this product?')">Delete</a>
</div>
</div>
<?php endwhile; ?>
</div>
</section>
</body>
</html>
