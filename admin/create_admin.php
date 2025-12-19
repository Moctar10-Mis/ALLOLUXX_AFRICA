<?php
// admin/create_admin.php — RUN ONCE and DELETE IT afterward
include '../php/config.php';

$email = 'mou@gmail.com';
$name  = 'Mou Admin';
$plain = '12345';
$hash = password_hash($plain, PASSWORD_DEFAULT);

// check existing
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // update existing
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, password = ?, is_admin = 1 WHERE email = ?");
    $stmt->bind_param("sss", $name, $hash, $email);
    $ok = $stmt->execute();
    echo $ok ? "Admin updated\n" : "Update failed: " . $stmt->error . "\n";
} else {
    // create new
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, is_admin, created_at) VALUES (?, ?, ?, 1, NOW())");
    $stmt->bind_param("sss", $name, $email, $hash);
    $ok = $stmt->execute();
    echo $ok ? "Admin created\n" : "Insert failed: " . $stmt->error . "\n";
}