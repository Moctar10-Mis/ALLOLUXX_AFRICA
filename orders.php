<?php
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Include config.php from php folder
require_once __DIR__ . '/php/config.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

// Determine dashboard based on gender
$dashboard = (isset($_SESSION['gender']) && $_SESSION['gender'] === 'male') ? 'man_dashboard.php' : 'woman_dashboard.php';

// Get cart items from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background: #ffe6f0; /* soft luxury pink */
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #d63384;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .orders-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(214, 51, 132, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #f2d6e5;
        }

        th {
            background: linear-gradient(90deg, #ff99cc, #ff66b3);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:nth-child(even) {
            background-color: #ffe6f0;
        }

        tr:hover {
            background-color: #ffd6eb;
        }

        .grand-total {
            text-align: right;
            font-weight: bold;
            font-size: 1.1em;
            background-color: #ffe6f0;
            color: #d63384;
        }

        p.empty {
            text-align: center;
            font-size: 1.1em;
            color: #d63384;
            margin-top: 30px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #ff66b3;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .back-button:hover {
            background: #d63384;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <a href="<?php echo $dashboard; ?>" class="back-button">← Back to Dashboard</a>
        <h1>Your Orders</h1>

        <?php if(empty($cart)): ?>
            <p class="empty">You have no products in your cart.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach($cart as $product_id => $item):
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="grand-total">Grand Total:</td>
                        <td class="grand-total">$<?php echo number_format($grandTotal, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
