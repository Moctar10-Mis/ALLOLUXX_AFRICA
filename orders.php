<?php
// Enable error reporting
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

// Fetch orders from database for this user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background: #ffe6f0; /* luxury pink */
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
            max-width: 950px;
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
        table img {
            width: 80px;
            border-radius: 8px;
            object-fit: cover;
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

        <?php if($result->num_rows == 0): ?>
            <p class="empty">You have no orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Images</th>
                        <th>Total Price</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Ordered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php 
                                if(!empty($row['images'])){
                                    $images = explode(',', $row['images']); // comma-separated images
                                    foreach($images as $img): ?>
                                        <img src="<?php echo trim($img); ?>" alt="Product">
                                    <?php endforeach; 
                                } else {
                                    echo "No image";
                                }
                                ?>
                            </td>
                            <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo date("d M Y H:i", strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
