<?php
session_start();
include('php/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD TO CART HANDLER
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['image']
            ];
        }
    }

    header("Location: cart.php");
    exit();
}

// REMOVE ITEM
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// Update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = intval($id);
        $qty = max(1, intval($qty));
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }
    header("Location: cart.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Montserrat', sans-serif; margin:0; padding:0; background:linear-gradient(135deg,#fbd3e9,#bb377d); color:#6b21a8; }
header { background:#fff; padding:20px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
header h1 { margin:0; color:#a855f7; }
.btn { padding:10px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; margin:5px 0; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
.btn-primary { background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; }
.btn-danger { background:#ef4444; color:#fff; }
.btn-back { background:#9ca3af; color:#fff; }
.container { width:90%; max-width:900px; margin:30px auto; padding:20px; border-radius:12px; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,.1);}
h2 { text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#f3e8ff; color:#6b21a8; }
input[type="number"] { width:60px; padding:5px; border-radius:6px; border:1px solid #ddd; text-align:center; }
.bottom-actions { margin-top:20px; text-align:center; }
</style>
</head>
<body>

<header>
    <h1>ALLOLUX AFRICA</h1>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</header>

<div class="container">
    <h2>Your Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p style="text-align:center;">Your cart is empty. <a href="index.php" class="btn btn-primary">Continue Shopping</a></p>
    <?php else: ?>
        <form method="POST">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($_SESSION['cart'] as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>$<?= number_format($item['price'],2) ?></td>
                    <td>
                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1">
                    </td>
                    <td>$<?= number_format($subtotal,2) ?></td>
                    <td>
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-danger">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong>$<?= number_format($total,2) ?></strong></td>
                </tr>
            </table>
            <div style="text-align:center;">
                <button type="submit" name="update" class="btn btn-primary">Update Quantities</button>
            </div>
        </form>

        <div style="text-align:center; margin-top:20px;">
            <a href="checkout.php" class="btn btn-primary">Proceed to Payment</a>
        </div>
    <?php endif; ?>

    <div class="bottom-actions">
        <a href="man_dashboard.php" class="btn btn-back">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
