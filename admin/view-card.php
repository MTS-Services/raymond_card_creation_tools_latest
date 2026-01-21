<?php
require_once '../Middleware/Authentication.php';
require_once '../config/database.php';

new Authentication;

$card_id = (int)($_GET['id'] ?? 0);

if (!$card_id) {
    header('Location: cards.php');
    exit;
}

// Get card details
$stmt = $pdo->prepare("SELECT * FROM id_cards WHERE id = ?");
$stmt->execute([$card_id]);
$card = $stmt->fetch();

if (!$card) {
    header('Location: cards.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Card - <?= htmlspecialchars($card['full_name']) ?></title>
  	<link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }
        
        .card-wrapper {
            perspective: 1000px;
            margin: 30px auto;
            max-width: 900px;
        }
        
        .id-card {
            width: 600px;
            height: 380px;
            margin: 0 auto 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
            background: white;
        }
        
        .id-card:hover {
            transform: rotateY(5deg) rotateX(5deg);
        }
        
        /* Service Dog Card Styles */
        .service-dog-card {
            background: linear-gradient(135deg, #4a4a9b 0%, #6b46c1 100%);
            color: white;
        }
        
        .service-dog-card.red {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .service-dog-card.blue {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .service-dog-header {
            background: #4a4a9b;
            text-align: center;
            padding: 8px 20px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            border-bottom: 2px solid #fff;
        }
        
        .service-dog-header.red {
            background: #dc3545;
        }
        
        .service-dog-header.blue {
            background: #007bff;
        }
        
        .service-dog-body {
            padding: 15px 20px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .service-dog-photo {
            width: 140px;
            height: 160px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .service-dog-info {
            flex: 1;
            font-size: 16px;
            line-height: 1.3;
        }
        
        .service-dog-info h3 {
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 2px;
        }
        
        .service-info-text {
            font-size: 11px;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .animal-info {
            margin: 15px 0;
        }
        
        .animal-info div {
            margin: 3px 0;
        }
        
        .emergency-notice {
            background: #4a4a9b;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 11px;
            font-weight: bold;
            margin: 10px -20px -15px -20px;
        }
        
        .emergency-notice.red {
            background: #dc3545;
        }
        
        .emergency-notice.blue {
            background: #007bff;
        }
        
        .service-badge {
            position: absolute;
            right: 20px;
            top: 100px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255,255,255,0.4);
        }
        
        .service-badge img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        
        /* Child ID Card Styles */
        .child-id-card {
            background: white;
            color: #333;
            border: 2px solid #dc3545;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .child-id-card.red {
            border-color: #dc3545;
        }
        
        .child-id-card.blue {
            border-color: #007bff;
        }
        
        .child-id-header {
            background: #dc3545;
            text-align: center;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            color: white;
            border-radius: 18px 18px 0 0;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-header.red {
            background: #dc3545;
        }
        
        .child-id-header.blue {
            background: #007bff;
        }
        
        .child-id-body {
            background: transparent;
            color: #333;
            padding: 20px;
            height: calc(100% - 60px);
            display: flex;
            gap: 20px;
            position: relative;
            z-index: 2;
            font-family: 'Arial', sans-serif;
        }
        
        .child-photo {
            width: 120px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #333;
            position: relative;
            z-index: 3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .child-photo-container {
            position: relative;
            width: 120px;
            height: 140px;
            margin-right: 5px;
            z-index: 3;
        }
        
        .child-photo-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
            object-fit: cover;
            z-index: 1;
            opacity: 0.4;
            filter: blur(0.5px);
            transform: scale(1.05);
        }
        
        .child-info {
            flex: 1;
            padding-left: 10px;
            position: relative;
            z-index: 3;
        }
        
        .child-name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        .child-address {
            font-size: 12px;
            margin-bottom: 12px;
            line-height: 1.2;
            color: #333;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        .child-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin: 12px 0;
            font-size: 12px;
        }
        
        .child-details div {
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        .child-details span {
            font-weight: bold;
            color: #dc3545;
            margin-right: 6px;
            min-width: 40px;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        .child-details span.red {
            color: #dc3545;
        }
        
        .child-details span.blue {
            color: #007bff;
        }
        
        .parent-info {
            margin-top: 12px;
            font-size: 12px;
            line-height: 1.3;
            font-family: 'Arial', sans-serif;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        .parent-info strong {
            color: #dc3545;
        }
        
        .warning-banner {
            background: #dc3545;
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            margin: 0 -20px -20px -20px;
            border-radius: 0 0 18px 18px;
            font-family: 'Arial', sans-serif;
            position: relative;
            z-index: 3;
        }
        
        .child-decorations {
            position: absolute;
            right: 15px;
            top: 60px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 3;
        }
        
        /* Make decorations look more like stick figures */
        .child-decoration i {
            font-size: 12px;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3));
        }
        
        /* Card Back Styles */
        .card-back {
            background: #4a4a9b;
            color: white;
            padding: 20px;
            height: 100%;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .card-back.red {
            background: #dc3545;
        }
        
        .card-back.blue {
            background: #007bff;
        }
        
        .back-header {
            background: #4a4a9b;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 16px;
            margin: -20px -20px 20px -20px;
        }
        
        .back-header.red {
            background: #dc3545;
        }
        
        .back-header.blue {
            background: #007bff;
        }
        
        .law-section {
            margin-bottom: 15px;
        }
        
        .law-title {
            font-weight: bold;
            font-size: 12px;
            color: #ffd700;
            margin-bottom: 5px;
        }
        
        .qr-section {
            position: absolute;
            right: 20px;
            bottom: 20px;
            text-align: center;
        }
        
        .qr-code-canvas {
            border: 2px solid white;
            border-radius: 5px;
            background: white;
            padding: 3px;
        }
        
        .contact-info {
            margin-top: 10px;
            font-size: 10px;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .card-wrapper, .card-wrapper * {
                visibility: visible;
            }
            .card-wrapper {
                position: absolute;
                left: 0;
                top: 0;
            }
            .no-print {
                display: none !important;
            }
            .id-card {
                break-after: page;
                box-shadow: none;
                border: 1px solid #ccc;
            }
            
            /* Ensure colors print correctly */
            .child-id-header {
                background: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .warning-banner {
                background: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .child-id-back {
                background: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .child-details span {
                color: #dc3545 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .parent-info strong {
                color: #dc3545 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Emergency Medical Info Card Print Styles */
            .emergency-medical-header {
                background: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .emergency-medical-label {
                background: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .emergency-medical-content {
                background: #f8f9fa !important;
                border-left-color: #007bff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        /* Child ID Card Back Styles */
        .child-id-back {
            background: #dc3545;
            color: white;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .child-id-back .back-header {
            background: #dc3545;
            text-align: center;
            padding: 15px;
            font-weight: bold;
            font-size: 18px;
            margin: -20px -20px 20px -20px;
            font-family: 'Arial', sans-serif;
            line-height: 1.2;
        }
        
        .child-id-back .back-content {
            padding: 0 20px 20px 20px;
            font-size: 14px;
            line-height: 1.5;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-back .back-content div {
            margin-bottom: 10px;
        }
        
        .child-id-back .back-content div:last-of-type {
            margin-bottom: 20px;
        }
        
        .child-id-back .notes-section {
            background: white;
            color: #333;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-back .notes-label {
            background: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            margin-right: 10px;
            font-weight: bold;
            font-size: 12px;
        }
        /* Additional Child ID Card Styling */
        .child-id-card .child-photo-container {
            position: relative;
            width: 120px;
            height: 140px;
            margin-right: 5px;
        }
        
        .child-id-card .child-info {
            flex: 1;
            padding-left: 10px;
        }
        
        .child-id-card .child-name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-card .child-address {
            font-size: 12px;
            margin-bottom: 12px;
            line-height: 1.2;
            color: #333;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-card .child-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin: 12px 0;
            font-size: 12px;
        }
        
        .child-id-card .child-details div {
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-card .child-details span {
            font-weight: bold;
            color: #dc3545;
            margin-right: 6px;
            min-width: 40px;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-card .parent-info {
            margin-top: 12px;
            font-size: 12px;
            line-height: 1.3;
            font-family: 'Arial', sans-serif;
        }
        
        .child-id-card .parent-info strong {
            color: #dc3545;
        }
        
        .child-id-card .warning-banner {
            background: #dc3545;
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            margin: 0 -20px -20px -20px;
            border-radius: 0 0 18px 18px;
            font-family: 'Arial', sans-serif;
        }
        /* Emergency Medical Info Card Styles */
        .emergency-medical-card {
            background: white;
            color: #333;
            border: 2px solid #dc3545;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .emergency-medical-header {
            background: #dc3545;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 18px;
            color: white;
            font-family: 'Arial', sans-serif;
        }
        
        .emergency-medical-body {
            padding: 20px;
            position: relative;
            font-family: 'Arial', sans-serif;
        }
        
        .emergency-medical-section {
            margin-bottom: 20px;
        }
        
        .emergency-medical-label {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .emergency-medical-content {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            min-height: 40px;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .emergency-medical-background {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 1;
            color: #ccc;
        }
        
        .emergency-medical-footer {
            position: absolute;
            bottom: 10px;
            right: 20px;
            font-size: 12px;
            color: #333;
            font-weight: bold;
        }
        /* Child ID Card Background Image Styles */
        .child-card-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
            border-radius: 20px;
            pointer-events: none;
        }
        
        .child-card-background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 1;
            filter: none;
            display: block;
            margin-top: 30px;
        }
        
        /* Ensure text remains readable over background */
        .child-id-header {
            background: #dc3545;
            text-align: center;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            color: white;
            border-radius: 18px 18px 0 0;
            font-family: 'Arial', sans-serif;
            position: relative;
            z-index: 3;
        }
        
        .child-id-body {
            background: transparent;
            color: #333;
            padding: 20px;
            height: calc(100% - 60px);
            display: flex;
            gap: 20px;
            position: relative;
            z-index: 2;
            font-family: 'Arial', sans-serif;
        }
        
        .child-photo {
            width: 120px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #333;
            position: relative;
            z-index: 3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .child-photo-container {
            position: relative;
            width: 120px;
            height: 140px;
            margin-right: 5px;
            z-index: 3;
        }
        
        .child-info {
            flex: 1;
            padding-left: 10px;
            position: relative;
            z-index: 3;
        }
        
        .child-name {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-family: 'Arial', sans-serif;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .child-address {
            font-size: 12px;
            margin-bottom: 12px;
            line-height: 1.2;
            color: #333;
            font-family: 'Arial', sans-serif;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .child-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin: 12px 0;
            font-size: 12px;
        }
        
        .child-details div {
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .child-details span {
            font-weight: bold;
            color: #333;
            margin-right: 6px;
            min-width: 40px;
            font-family: 'Arial', sans-serif;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .parent-info {
            margin-top: 12px;
            font-size: 12px;
            line-height: 1.3;
            font-family: 'Arial', sans-serif;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .parent-info strong {
            color: #333;
        }
        
        .warning-banner {
            background: #dc3545;
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            margin: 0 -20px -20px -20px;
            border-radius: 0 0 18px 18px;
            font-family: 'Arial', sans-serif;
            position: relative;
            z-index: 3;
            text-shadow: 2px 2px 4px rgba(255,255,255,0.9);
        }
        
        .child-decorations {
            position: absolute;
            right: 15px;
            top: 60px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 3;
        }
        
        .child-decoration {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .child-decoration:nth-child(1) { background: #4CAF50; }
        .child-decoration:nth-child(2) { background: #FF9800; }
        .child-decoration:nth-child(3) { background: #E91E63; }
        .child-decoration:nth-child(4) { background: #9C27B0; }
        .child-decoration:nth-child(5) { background: #3F51B5; }
        .child-decoration:nth-child(6) { background: #009688; }
        .child-decoration:nth-child(7) { background: #FF5722; }
        
        /* Make decorations look more like stick figures */
        .child-decoration i {
            font-size: 12px;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3));
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3 pb-2 mb-3 no-print">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">View ID Card</h1>
                        <div>
                            <a href="edit-card.php?id=<?= $card['id'] ?>" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Edit Card
                            </a>
                            <button onclick="window.print()" class="btn btn-success me-2">
                                <i class="fas fa-print me-2"></i>Print Card
                            </button>
                            <a href="cards.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cards
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-wrapper">
                    <?php if ($card['card_type'] == 'service_dog'): ?>
                        <!-- Service Dog Card Front -->
                        <div class="id-card service-dog-card <?= $card['card_color'] ?>">
                            <div class="service-dog-header <?= $card['card_color'] ?>">
                                RIGHTS PROTECTED UNDER U.S. FEDERAL LAW
                                </div>
                            <div class="service-dog-body">
                                <?php if ($card['photo']): ?>
                                    <img src="../<?= htmlspecialchars($card['photo']) ?>" alt="Photo" class="service-dog-photo">
                                <?php else: ?>
                                    <div class="service-dog-photo" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-dog fa-3x" style="color: #888;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="service-dog-info">
                                    <h3>SERVICE DOG</h3>
                                    <div class="service-info-text">
                                        Handler and Animal are protected under the following law:<br>
                                        <strong>Fair Housing Act (FHAct, 42 U.S.C.A. 3601 et seq)</strong><br>
                                        <?= htmlspecialchars($card['address'] ?: '123 North Main Street, Any City, ES 12345') ?><br>
                                        Telephone: <?= htmlspecialchars($card['phone'] ?: '123-456-7890') ?>
                                    </div>
                                    
                                    <div class="animal-info">
                                        <div><strong>Animal's Name:</strong></div>
                                        <div style="color: #ffd700; font-size: 20px; font-weight: bold;"><?= htmlspecialchars($card['animal_name'] ?: $card['full_name']) ?></div>
                                        <div><strong>Handler's Name:</strong></div>
                                        <div style="color: #ffd700; font-size: 20px; font-weight: bold;"><?= htmlspecialchars($card['handler_name'] ?: $card['full_name']) ?></div>
                                    </div>
                                </div>
                                
                                <div class="service-badge">
                                    <img src="service-badge.png" alt="Official Seal">
                                </div>
                            </div>
                            <div class="emergency-notice <?= $card['card_color'] ?>">
                                In case of emergency, do not separate service animal from handler. They are to be transported together.
                            </div>
                            
                            <!-- QR Code and Registry Info -->
                            <div style="position: absolute; bottom: 15px; left: 20px; display: flex; align-items: center; gap: 15px;">
                                <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3);">
                                    <img src="data:image/svg+xml,<?= urlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="#1f2937" stroke="#ffd700" stroke-width="3"/><circle cx="50" cy="50" r="35" fill="#374151"/><text x="50" y="35" text-anchor="middle" fill="#ffd700" font-size="8" font-weight="bold">UNITED STATES</text><text x="50" y="45" text-anchor="middle" fill="#ffd700" font-size="6">SERVICE DOG</text><text x="50" y="55" text-anchor="middle" fill="#ffd700" font-size="6">REGISTRY</text><text x="50" y="70" text-anchor="middle" fill="#ffd700" font-size="4">OFFICIAL SEAL</text></svg>') ?>" width="40" height="40" alt="Seal">
                                </div>
                                <div style="background: white; padding: 5px; border-radius: 5px; text-align: center; color: #333; font-size: 10px; font-weight: bold;">
                                    United States Service<br>
                                    Dog Registry ID Number<br>
                                    <span style="color: #ff0000; font-size: 14px;"><?= htmlspecialchars($card['registry_number'] ?: '1234567890') ?></span>
                                            </div>
                                <div style="color: white; font-size: 24px;">✈</div>
                                <div style="color: white; font-size: 24px;">🏠</div>
                                <div style="color: white; font-size: 24px;">🚫</div>
                                <canvas id="qrcode" width="60" height="60" class="qr-code-canvas"></canvas>
                                        </div>
                                    </div>
                                    
                        <!-- Service Dog Card Back -->
                        <div class="id-card card-back <?= $card['card_color'] ?>">
                            <div class="back-header <?= $card['card_color'] ?>">
                                UNITED STATES FEDERAL LAW
                                        </div>
                                        
                            <div style="margin-bottom: 15px;">
                                <strong>Handler in possession of this card is protected by the following laws:</strong>
                                        </div>
                                        
                            <div class="law-section">
                                <div class="law-title">Americans with Disabilities Act</div>
                                <div>Under the ADA, state and local governments, businesses, and nonprofit organizations that serve the public generally must allow service animals to accompany people with disabilities in all areas of the facility where the public is normally allowed to go.</div>
                                            </div>
                            
                            <div class="law-section">
                                <div class="law-title">Air Carrier Access Act (ACAA, 49 U.S.C. 41705)</div>
                                <div>The ACAA prohibits discrimination by U.S. and foreign air carriers on the basis of physical or mental disability. The ACAA protects individuals by allowing emotional support animals to fly with them in the aircraft cabin (without having to pay any additional fees).</div>
                                            </div>
                            
                            <div class="law-section">
                                <div class="law-title">Fair Housing Amendments Act</div>
                                <div>The FHAA protects individuals by allowing their emotional support animals to live with them (even when a no pet policy exists).</div>
                                            </div>
                            
                            <div class="contact-info">
                                <strong>Questions? US Department of Justice | 1-234-567-8900</strong><br>
                                <strong>Visit www.myussar.com for registration look-up or SCAN QR CODE</strong><br>
                                Affiliate ID: [ID]
                                            </div>
                                            
                            <div class="qr-section">
                                <canvas id="qrcode-back" width="80" height="80" class="qr-code-canvas"></canvas>
                                <div style="margin-top: 5px; font-size: 10px;">ShieldId.us</div>
                                                        </div>
                                            </div>
                                            
                    <?php elseif ($card['card_type'] == 'child_id'): ?>
                        <!-- Child ID Card Front -->
                        <div class="id-card child-id-card <?= $card['card_color'] ?>">
                            <!-- Background Image for entire card front -->
                            <div class="child-card-background">
                                <img src="child-id-background.jpg" alt="Background" class="child-card-background-image">
                            </div>
                            
                            <div class="child-id-header">
                                CHILD IDENTIFICATION CARD
                            </div>
                            <div class="child-id-body">
                                <div class="child-photo-container">
                                    <?php if ($card['photo']): ?>
                                        <!-- Main photo overlay -->
                                        <img src="../<?= htmlspecialchars($card['photo']) ?>" alt="Photo" class="child-photo">
                                    <?php else: ?>
                                        <div class="child-photo" style="background: #ddd; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-child fa-3x" style="color: #888;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="child-info">
                                    <div class="child-name"><?= htmlspecialchars($card['full_name']) ?></div>
                                    <div class="child-address">
                                        <?= nl2br(htmlspecialchars($card['address'] ?: '1200 Elizabeth Ave\nPlantation, FL 12345')) ?>
                                    </div>
                                    
                                    <div class="child-details">
                                        <div><span>DOB</span>: <?= $card['date_of_birth'] ? date('m.d.Y', strtotime($card['date_of_birth'])) : '01.08.2010' ?></div>
                                        <div><span>Sex</span>: <?= htmlspecialchars($card['gender'] ?? 'M') ?></div>
                                        <div><span>Hair</span>: <?= htmlspecialchars($card['hair_color'] ?? 'Brown') ?></div>
                                        <div><span>Eyes</span>: <?= htmlspecialchars($card['eye_color'] ?? 'Blue') ?></div>
                                        <div><span>Height</span>: <?= htmlspecialchars($card['height'] ?? "4'8\"") ?></div>
                                        <div><span>Weight</span>: <?= htmlspecialchars($card['weight'] ?? '48LBS') ?></div>
                                        <div><span>Blood</span>: <?= htmlspecialchars($card['blood_type'] ?? 'O(+)') ?></div>
                                    </div>
                                    
                                    <div class="parent-info">
                                        <strong>Parents:</strong> <?= htmlspecialchars($card['parent_guardian'] ?? 'Ashley and Robert Rossen') ?><br>
                                        <strong>Mom</strong> (phone): <?= htmlspecialchars($card['parent_phone'] ?? '012 345 6789') ?><br>
                                        <strong>Dad</strong> (phone): <?= htmlspecialchars($card['emergency_contact_1_phone'] ?? '012 345 6789') ?>
                                    </div>
                                </div>
                                
                    
                                <!-- QR Code on Front of Child ID Card -->
                                <div style="position: absolute; bottom: 20px; left: 20px; text-align: center; background: rgba(255,255,255,0.9); padding: 8px; border-radius: 8px;">
                                    <canvas id="qrcode-child-front" width="60" height="60" class="qr-code-canvas"></canvas>
                                    <div style="margin-top: 5px; font-size: 10px; color: #333;">ShieldId.us</div>
                                    <!-- Fallback QR code display -->
                                    <div id="qrcode-child-front-fallback" style="display: none;">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=<?= urlencode('https://shieldid.us/view-card.php?token=' . ($card['qr_random_number'] ?? 'default') . '&id=' . ($card['card_number'] ?? $card['id'] ?? 'default')) ?>" alt="QR Code" style="width: 60px; height: 60px;">
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($card['allergies']): ?>
                                <div class="warning-banner">
                                    WARNING: <?= strtoupper(htmlspecialchars($card['allergies'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                                                <!-- Child ID Card Back -->
                        <div class="id-card" style="background: #dc3545; color: white; padding: 20px;">
                            <div style="background: #dc3545; text-align: center; padding: 15px; font-weight: bold; font-size: 18px; margin: -20px -20px 20px -20px;">
                                WHAT TO DO IF YOUR<br>CHILD IS MISSING
                            </div>
                            
                            <div style="font-size: 14px; line-height: 1.5;">
                                <div style="margin-bottom: 10px;">
                                    <strong>1.</strong> When a child is missing act quickly, time is of the essence.
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <strong>2.</strong> Call the police immediately, show them this card.
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <strong>3.</strong> Organize a search for your child as quickly as possible.
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <strong>4.</strong> Check your child's favorite play areas, have someone else check them again.
                                </div>
                            </div>
                            
                            <div style="background: white; color: #333; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                <strong style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; margin-right: 10px;">NOTES</strong>
                                <?= htmlspecialchars($card['allergies'] ?: 'SHELLFISH ALLERGY') ?>
                            </div>
                            
                            <div style="position: absolute; bottom: 20px; right: 20px; text-align: center;">
                                <canvas id="qrcode-child-back" width="80" height="80" class="qr-code-canvas"></canvas>
                                <div style="margin-top: 5px; font-size: 10px;">ShieldId.us</div>
                            </div>
                            
                            <div style="position: absolute; bottom: 10px; left: 20px; font-size: 12px;">
                                ShieldId.us
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Emergency ID Card -->
                        <div class="id-card" style="background: white; color: #333; border: 2px solid #dc3545; overflow: hidden;">
                            <!-- Top Header -->
                            <div style="background: #dc3545; text-align: center; padding: 8px; font-weight: bold; font-size: 16px; color: white; border-radius: 18px 18px 0 0;">
                                EMERGENCY ID CARD
                            </div>
                            
                            <!-- Main Body - Personal Information -->
                            <div style="padding: 15px; text-align: center;">
                                <!-- Name -->
                                <div style="font-size: 20px; font-weight: bold; margin-bottom: 8px; color: #333;">
                                    <?= htmlspecialchars($card['full_name']) ?>
                                </div>
                                
                                <!-- Address -->
                                <div style="font-size: 12px; margin-bottom: 15px; color: #333; line-height: 1.3;">
                                    <?= nl2br(htmlspecialchars($card['address'])) ?>
                                </div>
                                
                                <!-- Personal Details in Two Columns -->
                                <div style="display: flex; justify-content: center; gap: 30px; margin-bottom: 15px;">
                                    <!-- Left Column -->
                                    <div style="text-align: left; font-size: 12px;">
                                        <div style="margin-bottom: 6px;"><strong>DOB:</strong> <?= $card['date_of_birth'] ? date('m/d/Y', strtotime($card['date_of_birth'])) : '7/10/2016' ?></div>
                                        <div style="margin-bottom: 6px;"><strong>Height:</strong> <?= htmlspecialchars($card['height'] ?: "3'11\"") ?></div>
                                    </div>
                                    
                                    <!-- Right Column -->
                                    <div style="text-align: left; font-size: 12px;">
                                        <div style="margin-bottom: 6px;"><strong>Blood Type:</strong> <?= htmlspecialchars($card['blood_type'] ?: 'O+') ?></div>
                                        <div style="margin-bottom: 6px;"><strong>Weight:</strong> <?= htmlspecialchars($card['weight'] ?: '49 LB') ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bottom Header - Emergency Contacts -->
                            <div style="background: #dc3545; text-align: center; padding: 8px; font-weight: bold; font-size: 16px; color: white;">
                                Emergency Contacts
                            </div>
                            
                            <!-- Bottom Body - Contact Information -->
                            <div style="padding: 12px; display: flex; justify-content: center; gap: 20px;">
                                <!-- Left Column -->
                                <div style="text-align: center; font-size: 12px;">
                                    <div style="font-weight: bold; margin-bottom: 4px;"><?= htmlspecialchars($card['emergency_contact_1_name'] ?: 'Ryan Template') ?></div>
                                    <div style="font-size: 11px;"><?= htmlspecialchars($card['emergency_contact_1_phone'] ?: '(801) 123-4567') ?></div>
                                </div>
                                
                                <!-- Right Column -->
                                <div style="text-align: center; font-size: 12px;">
                                    <div style="font-weight: bold; margin-bottom: 4px;"><?= htmlspecialchars($card['emergency_contact_2_name'] ?: 'Lyan Template') ?></div>
                                    <div style="font-size: 11px;"><?= htmlspecialchars($card['emergency_contact_2_phone'] ?: '(801) 123-4567') ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Emergency Medical Info Card Back -->
                        <div class="id-card" style="background: white; color: #333; border: 2px solid #dc3545; overflow: hidden;">
                            <!-- Top Header -->
                            <div style="background: #dc3545; text-align: center; padding: 8px; font-weight: bold; font-size: 16px; color: white; border-radius: 18px 18px 0 0;">
                                <span style="color: white; margin: 0 8px; font-size: 14px;">✳</span>
                                EMERGENCY MEDICAL INFO
                                <span style="color: white; margin: 0 8px; font-size: 14px;">✳</span>
                            </div>
                            
                            <!-- Main Body with Star of Life Watermark -->
                            <div style="padding: 15px; position: relative;">
                                <!-- Background Star of Life Symbol -->
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.1; z-index: 1;">
                                    <i class="fas fa-star-of-life" style="font-size: 100px; color: #ccc;"></i>
                                </div>
                                
                                <div style="position: relative; z-index: 2;">
                                    <!-- Section 1: Allergies -->
                                    <div style="margin-bottom: 15px;">
                                        <div style="background: #007bff; color: white; padding: 6px 10px; border-radius: 4px; display: inline-block; margin-bottom: 6px; font-weight: bold; font-size: 12px;">
                                            Allergies
                                        </div>
                                        <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #007bff; min-height: 35px; font-size: 11px; line-height: 1.3;">
                                            <?= htmlspecialchars($card['allergies'] ?: 'List any allergies you have here, up to two full lines!') ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Section 2: Medical Concerns -->
                                    <div style="margin-bottom: 15px;">
                                        <div style="background: #007bff; color: white; padding: 6px 10px; border-radius: 4px; display: inline-block; margin-bottom: 6px; font-weight: bold; font-size: 12px;">
                                            Medical Concerns
                                        </div>
                                        <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #007bff; min-height: 35px; font-size: 11px; line-height: 1.3;">
                                            <?= htmlspecialchars($card['medical_conditions'] ?: 'List any medical concerns you have here, up to two full lines!') ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Section 3: Notes -->
                                    <div style="margin-bottom: 15px;">
                                        <div style="background: #007bff; color: white; padding: 6px 10px; border-radius: 4px; display: inline-block; margin-bottom: 6px; font-weight: bold; font-size: 12px;">
                                            Notes
                                        </div>
                                        <div style="padding: 8px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #007bff; min-height: 35px; font-size: 11px; line-height: 1.3;">
                                            <?= htmlspecialchars($card['notes'] ?: 'Here you can list any notes you want on your card. Medications, pets home alone, etc.') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div style="position: absolute; bottom: 8px; right: 15px; font-size: 10px; color: #333; font-weight: bold;">
                                    ShieldId.us
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // QR Code generation using multiple fallback methods
        document.addEventListener('DOMContentLoaded', function() {
            const qrData = 'https://shieldid.us/view-card.php?token=<?= htmlspecialchars($card['qr_random_number'] ?? 'default') ?>&id=<?= htmlspecialchars($card['card_number'] ?? $card['id'] ?? 'default') ?>';
            
            console.log('QR Code Data:', qrData);
            console.log('QR Random Number:', '<?= htmlspecialchars($card['qr_random_number'] ?? 'default') ?>');
            console.log('Card Number:', '<?= htmlspecialchars($card['card_number'] ?? $card['id'] ?? 'default') ?>');
            
            // Method 1: Try to load QRCode library from multiple CDNs
            function loadQRCodeLibrary() {
                return new Promise((resolve, reject) => {
                    // Try multiple CDN sources
                    const cdnSources = [
                        'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js',
                        'https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js',
                        'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js'
                    ];
                    
                    let currentIndex = 0;
                    
                    function tryLoad() {
                        if (currentIndex >= cdnSources.length) {
                            reject(new Error('All CDN sources failed'));
                            return;
                        }
                        
                        const script = document.createElement('script');
                        script.src = cdnSources[currentIndex];
                        script.onload = () => {
                            console.log('QRCode library loaded from:', cdnSources[currentIndex]);
                            resolve();
                        };
                        script.onerror = () => {
                            console.log('Failed to load from:', cdnSources[currentIndex]);
                            currentIndex++;
                            tryLoad();
                        };
                        document.head.appendChild(script);
                    }
                    
                    tryLoad();
                });
            }
            
            // Method 2: Generate QR codes using external API (fallback)
            function generateQRWithAPI(canvasId, size = 80) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                
                // Create fallback div
                const fallbackId = canvasId + '-fallback';
                let fallback = document.getElementById(fallbackId);
                
                if (!fallback) {
                    fallback = document.createElement('div');
                    fallback.id = fallbackId;
                    fallback.style.cssText = 'text-align: center; margin: 5px 0;';
                    canvas.parentNode.insertBefore(fallback, canvas.nextSibling);
                }
                
                // Generate QR code using external API
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(qrData)}&format=png`;
                
                fallback.innerHTML = `
                    <img src="${qrUrl}" alt="QR Code" style="width: ${size}px; height: ${size}px; border: 1px solid #ddd; border-radius: 4px;">
                    <div style="margin-top: 5px; font-size: 10px; color: #666;">ShieldId.us</div>
                `;
                
                // Hide canvas, show fallback
                canvas.style.display = 'none';
                fallback.style.display = 'block';
                
                console.log('QR code generated using API for:', canvasId);
            }
            
            // Method 3: Generate QR codes using local library
            function generateQRWithLibrary() {
                if (typeof QRCode === 'undefined') {
                    console.log('QRCode library not available, using API fallback');
                    generateAllQRCodesWithAPI();
                    return;
                }
                
                console.log('QRCode library available, generating QR codes...');
                
                // Generate QR codes for all canvas elements
                const qrCanvases = [
                    { id: 'qrcode', size: 60 },
                    { id: 'qrcode-back', size: 80 },
                    { id: 'qrcode-child-back', size: 80 },
                    { id: 'qrcode-child-front', size: 60 }
                ];
                
                qrCanvases.forEach(({ id, size }) => {
                    const canvas = document.getElementById(id);
                    if (canvas) {
                        try {
                            QRCode.toCanvas(canvas, qrData, {
                                width: size,
                                height: size,
                                margin: 1,
                                color: {
                                    dark: '#000000',
                                    light: '#FFFFFF'
                                }
                            });
                            console.log('QR code generated for:', id);
                        } catch (error) {
                            console.error('Error generating QR code for', id, ':', error);
                            generateQRWithAPI(id, size);
                        }
                    }
                });
            }
            
            // Method 4: Generate all QR codes using API
            function generateAllQRCodesWithAPI() {
                const qrCanvases = [
                    { id: 'qrcode', size: 60 },
                    { id: 'qrcode-back', size: 80 },
                    { id: 'qrcode-child-back', size: 80 },
                    { id: 'qrcode-child-front', size: 60 }
                ];
                
                qrCanvases.forEach(({ id, size }) => {
                    generateQRWithAPI(id, size);
                });
            }
            
            // Main execution
            loadQRCodeLibrary()
                .then(() => {
                    // Library loaded successfully
                    generateQRWithLibrary();
                })
                .catch((error) => {
                    console.log('QRCode library failed to load, using API fallback:', error);
                    generateAllQRCodesWithAPI();
                });
        });
    </script>
</body>
</html>