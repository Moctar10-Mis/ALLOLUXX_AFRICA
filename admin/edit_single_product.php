<?php
session_start();
include '../php/config.php';

// Check admin session
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Get product ID
if(!isset($_GET['id'])){
    header("Location: edit_product.php");
    exit();
}

$id = intval($_GET['id']);
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
$message = '';

// Update product
if(isset($_POST['update_product'])){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Handle new image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $targetDir = "../assets/images/$category/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)){
            $imagePath = "$category/$filename";
            $conn->query("UPDATE products SET name='$name', description='$description', price=$price, category='$category', image='$imagePath' WHERE id=$id");
            $message = "Product updated successfully!";
        } else {
            $message = "Failed to upload image.";
        }
    } else {
        $conn->query("UPDATE products SET name='$name', description='$description', price=$price, category='$category' WHERE id=$id");
        $message = "Product updated successfully!";
    }
    $product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product - Admin</title>
<link rel="stylesheet" href="../assets/css/form.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA Admin</h1>
        <nav>
            <a href="edit_product.php" class="btn btn-primary">Back</a>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>

<div class="form-container">
    <h2>Edit Product</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" value="<?php echo $product['name']; ?>" required>
        <textarea name="description" placeholder="Description" required><?php echo $product['description']; ?></textarea>
        <input type="number" name="price" placeholder="Price" step="0.01" value="<?php echo $product['price']; ?>" required>
        <select name="category" required>
            <option value="men" <?php if($product['category']=='men') echo 'selected'; ?>>Men</option>
            <option value="women" <?php if($product['category']=='women') echo 'selected'; ?>>Women</option>
        </select>
        <p>Current Image:</p>
        <img src="../assets/images/<?php echo $product['image']; ?>" width="150">
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="update_product">Update Product</button>
    </form>
</div>
</body>
</html>
