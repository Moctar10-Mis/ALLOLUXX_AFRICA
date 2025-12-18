<?php
include 'config.php';

// Read JSON from MoMo callback
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['status']) && isset($data['externalId'])){
    $status = $data['status'] === 'SUCCESSFUL' ? 'Paid' : 'Failed';

    // Update order in DB
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE transaction_id=?");
    $stmt->bind_param("ss", $status, $data['externalId']);
    $stmt->execute();
}

http_response_code(200); // acknowledge MoMo
?>
