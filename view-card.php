<?php
require_once 'config/database.php';

// Initialize database if needed
initializeDatabase();

$token = $_GET['token'] ?? '';
$card_number = $_GET['id'] ?? '';

if (!$token || !$card_number) {
    die("Invalid QR code. Please scan a valid ShieldID card.");
}

// Get card details by token and card number
try {
    $stmt = $pdo->prepare("SELECT * FROM id_cards WHERE qr_random_number = ? AND card_number = ?");
    $stmt->execute([$token, $card_number]);
    $card = $stmt->fetch();
    
    if (!$card) {
        die("Card not found or invalid QR code. Please scan a valid ShieldID card.");
    }
} catch(PDOException $e) {
    die("Error accessing card information. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShieldID Verification - <?= htmlspecialchars($card['full_name']) ?></title>
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }
        
        .verification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        
        .card-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: <?= $card['card_color'] === 'red' ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' : 'linear-gradient(135deg, #007bff 0%, #0056b3 100%)' ?>;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .verification-badge {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            min-width: 150px;
        }
        
        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
        }
        
        .photo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .card-photo {
            width: 150px;
            height: 180px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #ddd;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .footer-info {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
        }
        
        .shield-logo {
            width: 60px;
            height: 40px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Verification Header -->
    <div class="verification-header">
        <div class="container">
            <h1><i class="fas fa-shield-alt me-3"></i>ShieldID Verification</h1>
            <p class="mb-0">Official ID Card Verification System</p>
        </div>
    </div>
    
    <!-- Card Container -->
    <div class="container">
        <div class="card-container">
            <!-- Card Header -->
            <div class="card-header">
                <h2 class="mb-2">
                    <?php if ($card['card_type'] == 'service_dog'): ?>
                        SERVICE DOG IDENTIFICATION CARD
                    <?php elseif ($card['card_type'] == 'child_id'): ?>
                        CHILD IDENTIFICATION CARD
                    <?php else: ?>
                        EMERGENCY IDENTIFICATION CARD
                    <?php endif; ?>
                </h2>
                <p class="mb-0">Verified by ShieldID.us</p>
            </div>
            
            <!-- Card Body -->
            <div class="card-body">
                <div class="verification-badge">
                    <i class="fas fa-check-circle me-2"></i>VERIFIED IDENTIFICATION
                </div>
                
                <!-- Photo Section -->
                <div class="photo-section">
                    <?php if ($card['photo']): ?>
                        <img src="uploads/photos/<?= basename($card['photo']) ?>" alt="Photo" class="card-photo">
                    <?php else: ?>
                        <div class="card-photo bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Basic Information -->
                <div class="info-row">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value"><?= htmlspecialchars($card['full_name']) ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Card Number:</span>
                    <span class="info-value"><?= htmlspecialchars($card['card_number']) ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Card Type:</span>
                    <span class="info-value"><?= ucwords(str_replace('_', ' ', $card['card_type'])) ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Verification Token:</span>
                    <span class="info-value"><?= htmlspecialchars($card['qr_random_number']) ?></span>
                </div>
                
                <?php if ($card['date_of_birth']): ?>
                <div class="info-row">
                    <span class="info-label">Date of Birth:</span>
                    <span class="info-value"><?= date('M j, Y', strtotime($card['date_of_birth'])) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($card['address']): ?>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value"><?= nl2br(htmlspecialchars($card['address'])) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($card['phone']): ?>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?= htmlspecialchars($card['phone']) ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Type-specific information -->
                <?php if ($card['card_type'] == 'child_id'): ?>
                    <?php if ($card['parent_guardian']): ?>
                    <div class="info-row">
                        <span class="info-label">Parent/Guardian:</span>
                        <span class="info-value"><?= htmlspecialchars($card['parent_guardian']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($card['allergies']): ?>
                    <div class="info-row">
                        <span class="info-label">Allergies:</span>
                        <span class="info-value text-danger"><?= htmlspecialchars($card['allergies']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                <?php elseif ($card['card_type'] == 'service_dog'): ?>
                    <?php if ($card['animal_name']): ?>
                    <div class="info-row">
                        <span class="info-label">Animal's Name:</span>
                        <span class="info-value"><?= htmlspecialchars($card['animal_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($card['handler_name']): ?>
                    <div class="info-row">
                        <span class="info-label">Handler's Name:</span>
                        <span class="info-value"><?= htmlspecialchars($card['handler_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($card['registry_number']): ?>
                    <div class="info-row">
                        <span class="info-label">Registry Number:</span>
                        <span class="info-value"><?= htmlspecialchars($card['registry_number']) ?></span>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Emergency Contacts -->
                <?php if ($card['emergency_contact_1_name']): ?>
                <div class="info-row">
                    <span class="info-label">Emergency Contact:</span>
                    <span class="info-value"><?= htmlspecialchars($card['emergency_contact_1_name']) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($card['emergency_contact_1_phone']): ?>
                <div class="info-row">
                    <span class="info-label">Emergency Phone:</span>
                    <span class="info-value"><?= htmlspecialchars($card['emergency_contact_1_phone']) ?></span>
                </div>
                <?php endif; ?>
                
                <!-- Issue Date -->
                <div class="info-row">
                    <span class="info-label">Issue Date:</span>
                    <span class="info-value"><?= date('M j, Y', strtotime($card['created_at'])) ?></span>
                </div>
                
                <!-- Status -->
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="badge bg-<?= $card['status'] == 'active' ? 'success' : ($card['status'] == 'inactive' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($card['status']) ?>
                        </span>
                    </span>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer-info">
                <img src="logo.png" alt="ShieldID Logo" class="shield-logo">
                <p class="mb-2"><strong>This is an official ShieldID verification</strong></p>
                <p class="mb-0 small">Card verified on <?= date('M j, Y \a\t g:i A') ?> | ShieldID.us</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 