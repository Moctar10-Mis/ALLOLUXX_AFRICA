<?php
session_start();
include '../php/config.php';


// Protect admin page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $category = $_POST['category']; // man or woman
    $price = $_POST['price'];
    $size = trim($_POST['size']);
    $color = trim($_POST['color']);
    $description = trim($_POST['description']);

    // Image
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if (
        empty($name) || empty($category) || empty($price) ||
        empty($size) || empty($color) || empty($description) || empty($image)
    ) {
        $error = "All fields are required.";
    } else {

        // Choose folder
        $folder = ($category === 'man') ? 'men' : 'women';

        // Image path saved in DB
        $imagePath = "/$folder/" . basename($image);

        // Full server path
        $uploadPath = "../assets/images" . $imagePath;

        if (move_uploaded_file($tmp, $uploadPath)) {

            $stmt = $conn->prepare("
                INSERT INTO products (name, category, price, size, color, description, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssdssss",
                $name,
                $category,
                $price,
                $size,
                $color,
                $description,
                $imagePath
            );
            $stmt->execute();

            header("Location: admin_dashboard.php");
            exit();

        } else {
            $error = "Image upload failed.";
        }
    }
}


if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $category = $_POST['category']; // man or woman
    $price = $_POST['price'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $description = $_POST['description'];

    // Image upload
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    // Choose folder
    $folder = ($category === 'man') ? 'men' : 'women';

    // Save path IN DATABASE
    $imagePath = "$folder/" . $image;

    // Move image
    move_uploaded_file($tmp, "../assets/images/" . $imagePath);

    // Insert product
    $stmt = $conn->prepare("
        INSERT INTO products (name, category, price, size, color, description, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdssss",
        $name,
        $category,
        $price,
        $size,
        $color,
        $description,
        $imagePath
    );

    $stmt->execute();

    header("Location: admin_dashboard.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - ALOLLUXX AFRICA</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            background: #f4f6f9;
        }
        .container {
            width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #007BFF;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
        }
        a {
            display: block;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <nav>
    <h1 style="text-align:center; padding:20px; background-color:#007BFF; color:white;">ALOLLUXX AFRICA - Admin Panel</h1>

    <button type="submit" name ="add" style="text-align: center; padding:10px; background-color:green; color:white; border:none; border-radius:5px; hover: #0056; cursor:pointer; length:px; margin-bottom:20px;">
        <a href="../index.php">Go to Main Site</a></button>
    </nav>
</header>

<div class="container">
    <h2>Add Product</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

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

    <a href="admin_dashboard.php"> Back to Dashboard</a>
</div>

</body>
</html>
