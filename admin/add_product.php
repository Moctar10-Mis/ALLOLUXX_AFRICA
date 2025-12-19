<?php
session_start();
include '../php/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $size = trim($_POST['size']);
    $color = trim($_POST['color']);
    $description = trim($_POST['description']);

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if (empty($name) || empty($category) || empty($price) || empty($size) || empty($color) || empty($description) || empty($image)) {
        $error = "All fields are required.";
    } else {
        $folder = ($category === 'man') ? 'men' : 'women';
        $imagePath = $folder . '/' . basename($image); // store in DB
        $uploadPath = "../assets/images/" . $imagePath; // actual upload folder

        if (move_uploaded_file($tmp, $uploadPath)) {
            $stmt = $conn->prepare("
                INSERT INTO products (name, category, price, size, color, description, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssdssss", $name, $category, $price, $size, $color, $description, $imagePath);
            $stmt->execute();
            $success = "Product added successfully!";
        } else {
            $error = "Image upload failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Montserrat', sans-serif; background: linear-gradient(135deg, #fbd3e9, #bb377d); margin:0; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
.container { background:#fff; width:100%; max-width:500px; padding:50px 30px 30px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.15); display:flex; flex-direction:column; align-items:center; }
header h1 { color:#a855f7; font-size:36px; margin-bottom:35px; font-weight:900; text-transform:uppercase; letter-spacing:1px; text-align:center;}
h2 { color:#a855f7; font-size:22px; margin-bottom:25px; text-align:center;}
form { width:100%; display:flex; flex-direction:column; gap:15px; }
input, select, textarea { width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e5e7eb; font-size:14px; transition: all 0.3s ease; }
input:focus, select:focus, textarea:focus { outline:none; border-color:#f472b6; box-shadow:0 0 8px rgba(244,114,182,0.4); }
textarea { resize: vertical; min-height:80px; }
button { width:100%; padding:14px; border:none; border-radius:10px; background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; font-size:16px; font-weight:600; cursor:pointer; transition: all 0.3s ease;}
button:hover { background:linear-gradient(90deg,#ec4899,#db2777); transform:translateY(-2px);}
.error, .success { width:100%; padding:12px; border-radius:10px; font-weight:600; text-align:center; }
.error { background:#f87171; color:#fff; margin-bottom:15px; }
.success { background:#34d399; color:#fff; margin-bottom:15px; }
a.btn-back { margin-top:20px; display:inline-block; text-decoration:none; background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; padding:12px 20px; border-radius:10px; font-weight:600; text-align:center; transition: all 0.3s ease;}
a.btn-back:hover { background:linear-gradient(90deg,#ec4899,#db2777); transform:translateY(-2px);}
</style>
</head>
<body>
<div class="container">
    <header><h1>ALLOLUX AFRICA</h1></header>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <h2>Add Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="man">Men</option>
            <option value="woman">Women</option>
        </select>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="text" name="size" placeholder="Size (S, M, L)" required>
        <input type="text" name="color" placeholder="Color" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="image" accept=".jpg,.jpeg,.png" required>
        <button type="submit" name="add">Add Product</button>
    </form>
    <a href="admin_dashboard.php" class="btn-back"> Back to Dashboard</a>
</div>
</body>
</html>
