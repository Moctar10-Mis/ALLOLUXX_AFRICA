<?php
$apiKey = 'YOUR_SUBSCRIPTION_KEY';
$url = 'https://sandbox.momodeveloper.mtn.com/collection/token/'; // sandbox URL

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, 'YOUR_USER:YOUR_PASSWORD'); // replace if required
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Ocp-Apim-Subscription-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$token = $data['access_token'] ?? '';
return $token;
?>
