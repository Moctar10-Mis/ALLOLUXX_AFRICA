<?php
session_start();
require 'vendor/autoload.php';
include 'php/config.php';

if (!isset($_SESSION['stripe_client_secret'])) {
    header("Location: checkout.php");
    exit();
}

$clientSecret = $_SESSION['stripe_client_secret'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stripe Payment - ALLOLUX AFRICA</title>
<script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<h2>Complete your payment</h2>
<div id="card-element"></div>
<button id="payBtn">Pay</button>

<script>
var stripe = Stripe('pk_test_your_publishable_key_here'); // replace
var elements = stripe.elements();
var card = elements.create('card');
card.mount('#card-element');

document.getElementById('payBtn').addEventListener('click', async function(){
    const {error, paymentIntent} = await stripe.confirmCardPayment(
        '<?= $clientSecret ?>',
        {payment_method: {card: card}}
    );
    if(error) alert(error.message);
    else window.location.href = 'thankyou.php';
});
</script>
</body>
</html>
