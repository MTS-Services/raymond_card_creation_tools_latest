<?php
// Database configuration



$host = 'db5019002075.hosting-data.io';
$dbname = 'dbs14962592';
$username = 'dbu4026357';
$password = '77143Ray!@12345#123$%^7!088989';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Base URL for QR code links
$base_url = 'http://localhost/virtual_id2/final_virtual_id/New%20Cards/';

// Function to generate unique ID
function generateUniqueId() {
    // Generate simple numeric ID (same format as generateRandomID)
    $part1 = rand(10000, 99999); // 5 digit number
    $part2 = rand(10000, 99999); // 5 digit number
    return $part1 . $part2;
}

// Function to generate QR code URL
function generateQRCodeUrl($uniqueId) {
    global $base_url;
    return $base_url . 'view_card.php?id=' . $uniqueId;
}
?>