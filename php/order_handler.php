<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || empty($_SESSION['cart'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? 'COD';
$total_price = 0;

$ids = implode(',', array_keys($_SESSION['cart']));
$res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
$products = [];
while($row = $res->fetch_assoc()){
    $row['quantity'] = $_SESSION['cart'][$row['id']];
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total_price += $row['subtotal'];
    $products[] = $row;
}

// Insert into orders
$conn->query("INSERT INTO orders (user_id, total_price, payment_method, status, created_at) 
             VALUES ($user_id, $total_price, '$payment_method', 'Pending', NOW())");

$order_id = $conn->insert_id;

// Insert order items
foreach($products as $p){
    $pid = $p['id'];
    $qty = $p['quantity'];
    $price = $p['price'];
    $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) 
                 VALUES ($order_id, $pid, $qty, $price)");
}

// Clear cart
$_SESSION['cart'] = [];

header("Location: ../thankyou.php?order_id=$order_id");
exit();
?>
