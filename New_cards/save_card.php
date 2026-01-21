<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Debug: Log the received data
error_log("Received card data: " . json_encode($input));

try {
    // Use the unique ID from the frontend, or generate a new one if not provided
    $uniqueId = $input['uniqueId'] ?? generateUniqueId();
    // Generate QR code URL - use provided one if it exists, is not empty, and is not "1", otherwise generate
    $qrCodeUrl = (!empty($input['qrCodeUrl']) && $input['qrCodeUrl'] !== '1' && $input['qrCodeUrl'] !== 1) 
        ? $input['qrCodeUrl'] 
        : generateQRCodeUrl($uniqueId);
    
    error_log("Using unique ID: " . $uniqueId);
    error_log("Received qrCodeUrl from frontend: " . ($input['qrCodeUrl'] ?? 'NOT PROVIDED'));
    error_log("Using QR code URL: " . $qrCodeUrl);
    
    // Create storage directories if they don't exist
    $storageDir = 'stored_cards/';
    if (!file_exists($storageDir)) {
        mkdir($storageDir, 0777, true);
    }
    
    // Generate unique filenames
    $fileSuffix = $input['fileSuffix'] ?? '';
    $frontFilename = $uniqueId . $fileSuffix . '_front.png';
    $backFilename = $uniqueId . $fileSuffix . '_back.png';
    
    // Save front and back images
    $frontPath = $storageDir . $frontFilename;
    $backPath = $storageDir . $backFilename;
    
    // Decode base64 images and save
    if (isset($input['frontImage']) && isset($input['backImage'])) {
        $frontImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $input['frontImage']));
        $backImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $input['backImage']));
        
        file_put_contents($frontPath, $frontImageData);
        file_put_contents($backPath, $backImageData);
    }
    
    // Prepare card data for database
    $cardData = [
        'cardType' => $input['cardType'] ?? 'unknown',
        'dropdownCardTypeName' => $input['dropdownCardTypeName'] ?? '',
        'fields' => $input['fields'] ?? [],
        'generatedAt' => date('Y-m-d H:i:s')
    ];
    
    // Extract handler/animal name for display
    $displayName = '';
    if (isset($input['fields'])) {
        // For combo cards, try to get animal name first, then handler name
        if (isset($input['fields']['animalName']) && !empty($input['fields']['animalName'])) {
            $displayName = $input['fields']['animalName'];
        } elseif (isset($input['fields']['handlerName']) && !empty($input['fields']['handlerName'])) {
            $displayName = $input['fields']['handlerName'];
        } elseif (isset($input['fields']['childName']) && !empty($input['fields']['childName'])) {
            $displayName = $input['fields']['childName'];
        } elseif (isset($input['fields']['autismName']) && !empty($input['fields']['autismName'])) {
            $displayName = $input['fields']['autismName'];
        } elseif (isset($input['fields']['patientName']) && !empty($input['fields']['patientName'])) {
            $displayName = $input['fields']['patientName'];
        } elseif (isset($input['fields']['emergencyContactName']) && !empty($input['fields']['emergencyContactName'])) {
            $displayName = $input['fields']['emergencyContactName'];
        }
    }
    
    $cardData['displayName'] = $displayName;
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO cards (card_type, unique_id, qr_code_url, front_image_path, back_image_path, card_data) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $input['cardType'] ?? 'unknown',
        $uniqueId,
        $qrCodeUrl,
        $frontPath,
        $backPath,
        json_encode($cardData)
    ]);
    
    $cardId = $pdo->lastInsertId();
    
    // Insert field data
    if (isset($input['fields']) && is_array($input['fields'])) {
        $fieldStmt = $pdo->prepare("
            INSERT INTO card_fields (card_id, field_name, field_value) 
            VALUES (?, ?, ?)
        ");
        
        foreach ($input['fields'] as $fieldName => $fieldValue) {
            $fieldStmt->execute([$cardId, $fieldName, $fieldValue]);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Card saved successfully',
        'uniqueId' => $uniqueId,
        'qrCodeUrl' => $qrCodeUrl,
        'cardId' => $cardId
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error saving card: ' . $e->getMessage()
    ]);
}
?>