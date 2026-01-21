<?php
require_once 'config/database.php';

// Get a sample card
$stmt = $pdo->query("SELECT * FROM id_cards LIMIT 1");
$card = $stmt->fetch();

if (!$card) {
    die("No cards found in database");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .qr-test { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .qr-code { text-align: center; margin: 20px 0; }
        .qr-code img { border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>QR Code Test</h1>
    
    <div class="qr-test">
        <h3>Card Information:</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($card['full_name']) ?></p>
        <p><strong>Card Number:</strong> <?= htmlspecialchars($card['card_number']) ?></p>
        <p><strong>QR Token:</strong> <?= htmlspecialchars($card['qr_random_number']) ?></p>
        <p><strong>Card Color:</strong> <?= htmlspecialchars($card['card_color']) ?></p>
    </div>
    
    <div class="qr-test">
        <h3>QR Code Generated Using External API:</h3>
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('https://shieldid.us/view-card.php?token=' . $card['qr_random_number'] . '&id=' . $card['card_number']) ?>&format=png" alt="QR Code">
            <p><strong>Scan this QR code to verify it works!</strong></p>
        </div>
    </div>
    
    <div class="qr-test">
        <h3>QR Code Data:</h3>
        <p><code><?= htmlspecialchars('https://shieldid.us/view-card.php?token=' . $card['qr_random_number'] . '&id=' . $card['card_number']) ?></code></p>
    </div>
    
    <div class="qr-test">
        <h3>Test Instructions:</h3>
        <ol>
            <li>Use your phone's camera or QR code scanner app</li>
            <li>Point it at the QR code above</li>
            <li>It should open a link to your verification page</li>
            <li>If it works, the QR codes on your ID cards will work too!</li>
        </ol>
    </div>
    
    <div class="qr-test">
        <h3>Next Steps:</h3>
        <p>✅ If the QR code above scans correctly, your ID card QR codes are working!</p>
        <p>❌ If it doesn't scan, there might be an issue with the external API</p>
        <p><a href="admin/view-card.php?id=<?= $card['id'] ?>">← Back to View Card</a></p>
    </div>
</body>
</html> 