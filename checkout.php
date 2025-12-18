<?php
session_start();
include 'php/config.php';

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)){
    die("<p>Your cart is empty. <a href='cart.php'>Go back to cart</a></p>");
}

// Calculate total
$total = 0;
foreach($cart as $item){
    $total += $item['price'] * $item['quantity'];
}

// Handle payment submission
if(isset($_POST['pay'])){
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $status = 'Pending'; // Default pending

    if($payment_method === 'Card'){
        // Placeholder: Integrate Stripe/PayPal here
        // On success, set $status = 'Paid';
        $status = 'Paid';
    } elseif($payment_method === 'MoMo'){
        // Placeholder: Call MoMo API here
        // On success, set $status = 'Paid';
        $status = 'Paid';
    } else {
        // COD
        $status = 'Pending';
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $total, $payment_method, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach($cart as $item){
        $stmtItem->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmtItem->execute();
    }

    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['order_success'] = "Thank you! Your order was successful.";
    header("Location: thankyou.php");
    exit();

if($payment_method === 'MoMo'){
    $token = include 'php/momo_oauth.php';
    $transactionId = uniqid(); // unique transaction ID

    $callbackUrl = 'https://yourdomain.com/momo_callback.php';

    $postData = [
        'amount' => number_format($total,2,'.',''),
        'currency' => 'USD',
        'externalId' => $transactionId,
        'payer' => [
            'partyIdType' => 'MSISDN',
            'partyId' => $_POST['phone'] ?? '233000000000'
        ],
        'payerMessage' => 'Payment for ALLOLUX AFRICA order',
        'payeeNote' => 'Thank you for shopping!',
        'callbackUrl' => $callbackUrl
    ];

    $ch = curl_init('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'X-Reference-Id: ' . $transactionId,
        'X-Target-Environment: sandbox',
        'Ocp-Apim-Subscription-Key: YOUR_SUBSCRIPTION_KEY',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $status = 'Pending'; // mark as pending until MoMo confirms
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - ALLOLUX AFRICA</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<style>
body { font-family:'Montserrat',sans-serif; margin:0; padding:20px; background:linear-gradient(135deg,#fbd3e9,#bb377d); color:#6b21a8;}
h2 { text-align:center; margin-bottom:20px;}
table { width:100%; border-collapse: collapse; margin-bottom:20px; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
th, td { padding:12px; text-align:center; border-bottom:1px solid #eee;}
th { background:#f472b6; color:#fff;}
.btn { padding:10px 20px; background:linear-gradient(90deg,#f472b6,#ec4899); color:#fff; border:none; cursor:pointer; border-radius:8px; font-weight:600;}
.btn:hover { opacity:0.9; }
.payment-methods { margin:15px 0; display:flex; flex-direction:column;}
.payment-methods label { margin-bottom:10px; cursor:pointer;}
#cardDetails { display:none; margin-top:15px; background:#fff; padding:15px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
.card-field { margin-bottom:10px; padding:10px; border:1px solid #d1d5db; border-radius:8px; width:100%;}
</style>
</head>
<body>

<h2>Checkout</h2>

<table>
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
</tr>
<?php foreach($cart as $item): ?>
<tr>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td>$<?= number_format($item['price'],2) ?></td>
    <td><?= $item['quantity'] ?></td>
    <td>$<?= number_format($item['price']*$item['quantity'],2) ?></td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="3"><strong>Total</strong></td>
    <td><strong>$<?= number_format($total,2) ?></strong></td>
</tr>
</table>

<form id="checkoutForm" method="POST" action="checkout.php">
    <h3>Select Payment Method</h3>
    <div class="payment-methods">
        <label><input type="radio" name="payment_method" value="MoMo" required> Mobile Money</label>
        <label><input type="radio" name="payment_method" value="Card"> Card (Stripe/PayPal)</label>
        <label><input type="radio" name="payment_method" value="COD"> Cash on Delivery</label>
    </div>

    <div id="cardDetails">
        <input type="text" name="card_number" placeholder="Card Number" class="card-field" />
        <input type="text" name="expiry" placeholder="MM/YY" class="card-field" />
        <input type="text" name="cvc" placeholder="CVC" class="card-field" />
    </div>

    <button type="submit" name="pay" class="btn">Pay & Place Order</button>
</form>

<a href="cart.php" class="btn" style="display:inline-block; margin-top:15px;">Back to Cart</a>

<script>
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
const cardDetails = document.getElementById('cardDetails');
paymentRadios.forEach(radio=>{
    radio.addEventListener('change', function(){
        if(this.value==='Card'){
            cardDetails.style.display='block';
        } else {
            cardDetails.style.display='none';
        }
    });
});
</script>

<div id="momoDetails" style="display:none;">
    <label>Phone Number:</label>
    <input type="text" name="phone" placeholder="233XXXXXXXXX" required>
</div>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(radio=>{
    radio.addEventListener('change', e=>{
        document.getElementById('momoDetails').style.display = e.target.value==='MoMo'?'block':'none';
    });
});
</script>

</body>


</html>
