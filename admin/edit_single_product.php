<?php
session_start();
require_once '../php/config.php';

if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Invalid Product ID");

$product_id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
if (!$product) die("Product not found");

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($price)) $error_msg = "Name and Price are required.";
    elseif (!is_numeric($price) || $price < 0) $error_msg = "Price must be a positive number.";
    else {
        $image_name = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = "../assets/images/";
            $tmp_name = $_FILES['image']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];

            if (!in_array($ext, $allowed)) $error_msg = "Invalid image type.";
            else {
                $folder = ($product['category'] === 'man') ? 'men' : 'women';
                $image_name = $folder . '/' . time() . "_" . basename($_FILES['image']['name']);
                move_uploaded_file($tmp_name, $upload_dir . $image_name);
            }
        }

        if (!$error_msg) {
            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("sdssi", $name, $price, $description, $image_name, $product_id);
            if ($stmt->execute()) {
                $success_msg = "Product updated successfully!";
                $product['name'] = $name;
                $product['price'] = $price;
                $product['description'] = $description;
                $product['image'] = $image_name;
            } else $error_msg = "Failed to update product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product - ALLOLUX AFRICA</title>
<style>
body { font-family:"Segoe UI", Tahoma, sans-serif; margin:0; padding:0; background:linear-gradient(135deg,#f9a8d4,#ec4899,#be185d); min-height:100vh; }
header { text-align:center; padding:40px 0; color:#fff; font-size:36px; font-weight:900; letter-spacing:3px; text-transform:uppercase; text-shadow:2px 2px 8px rgba(0,0,0,0.3); }
.container { max-width:600px; margin:30px auto 50px; background:rgba(255,255,255,0.95); border-radius:20px; padding:45px 35px; box-shadow:0 20px 50px rgba(0,0,0,0.35); }
h2 { text-align:center; color:#be185d; margin-bottom:25px; font-size:28px; font-weight:800;}
form label { display:block; margin:12px 0 6px; font-weight:600; color:#4b5563;}
form input[type="text"], form input[type="number"], form textarea, form input[type="file"] { width:100%; padding:14px; margin-bottom:18px; border-radius:12px; border:1px solid #f9a8d4; font-size:15px; }
form input:focus, form textarea:focus, form input[type="file"]:focus { border-color:#ec4899; box-shadow:0 0 12px rgba(236,72,153,0.4); outline:none; }
form textarea { resize:vertical; }
.btn-primary { width:100%; padding:16px; background:linear-gradient(90deg,#f9a8d4,#ec4899,#be185d); color:#fff; border:none; border-radius:12px; font-size:16px; font-weight:700; cursor:pointer; }
.btn-primary:hover { background:linear-gradient(90deg,#ec4899,#be185d,#9d174d); transform:translateY(-2px); }
.success { background:#22c55e; color:#fff; padding:12px; border-radius:12px; margin-bottom:15px; text-align:center; }
.error { background:#ef4444; color:#fff; padding:12px; border-radius:12px; margin-bottom:15px; text-align:center; }
img.product-image { display:block; max-width:220px; margin:15px auto 20px; border-radius:15px; border:2px solid #f9a8d4; }
</style>
</head>
<body>
<header>ALLOLUX AFRICA</header>
<div class="container">
<h2>Edit Product</h2>
<?php if ($success_msg): ?><div class="success"><?= htmlspecialchars($success_msg) ?></div><?php endif; ?>
<?php if ($error_msg): ?><div class="error"><?= htmlspecialchars($error_msg) ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data">
<label for="name">Product Name</label>
<input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
<label for="price">Price ($)</label>
<input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" required>
<label for="description">Description</label>
<textarea name="description" id="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
<label for="image">Product Image</label>
<input type="file" name="image" id="image">
<?php if ($product['image']): ?>
<img class="product-image" src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
<?php endif; ?>
<button type="submit" class="btn-primary">Update Product</button>
</form>
</div>
</body>
</html>
