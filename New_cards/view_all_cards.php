<?php
require_once '../Middleware/Authentication.php';
require_once 'config.php';

new Authentication;
// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Function to create thumbnail
function createThumbnail($imagePath, $thumbnailPath, $maxWidth = 150, $maxHeight = 100) {
    if (!file_exists($imagePath)) {
        return false;
    }

    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) {
        return false;
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $type = $imageInfo[2];

    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);

    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($imagePath);
            break;
        default:
            return false;
    }

    if (!$source) {
        return false;
    }

    // Create thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize image
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save thumbnail
    $thumbnailDir = 'thumbnails/';
    if (!is_dir($thumbnailDir)) {
        mkdir($thumbnailDir, 0755, true);
    }

    $thumbnailPath = $thumbnailDir . basename($imagePath);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $thumbnailPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $thumbnailPath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $thumbnailPath);
            break;
    }

    // Clean up
    imagedestroy($source);
    imagedestroy($thumbnail);

    return $thumbnailPath;
}

try {
    // Fetch all cards from database, grouped by unique_id to handle combo cards
    $stmt = $pdo->query("
        SELECT c.*, 
               GROUP_CONCAT(CONCAT(cf.field_name, ':', cf.field_value) SEPARATOR '|') as fields,
               COUNT(DISTINCT c.card_type) as card_count,
               GROUP_CONCAT(DISTINCT c.card_type SEPARATOR ',') as card_types
        FROM cards c 
        LEFT JOIN card_fields cf ON c.id = cf.card_id 
        GROUP BY c.unique_id 
        ORDER BY c.created_at DESC
    ");
    
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process cards data
    foreach ($cards as &$card) {
        // Parse fields
        $fields = [];
        if ($card['fields']) {
            $fieldPairs = explode('|', $card['fields']);
            foreach ($fieldPairs as $pair) {
                if (strpos($pair, ':') !== false) {
                    list($name, $value) = explode(':', $pair, 2);
                    $fields[$name] = $value;
                }
            }
        }
        $card['parsed_fields'] = $fields;
        
        // Parse card_data to get dropdown card type name and display name
        $cardData = json_decode($card['card_data'], true);
        $card['dropdown_card_type_name'] = $cardData['dropdownCardTypeName'] ?? '';
        $card['display_name'] = $cardData['displayName'] ?? '';
        
        // Create thumbnails if they don't exist
        if ($card['front_image_path'] && file_exists($card['front_image_path'])) {
            $frontThumbnail = 'thumbnails/' . basename($card['front_image_path']);
            if (!file_exists($frontThumbnail)) {
                createThumbnail($card['front_image_path'], $frontThumbnail);
            }
            $card['front_thumbnail'] = $frontThumbnail;
        }
        
        if ($card['back_image_path'] && file_exists($card['back_image_path'])) {
            $backThumbnail = 'thumbnails/' . basename($card['back_image_path']);
            if (!file_exists($backThumbnail)) {
                createThumbnail($card['back_image_path'], $backThumbnail);
            }
            $card['back_thumbnail'] = $backThumbnail;
        }
    }
    
} catch (Exception $e) {
    $error = 'Error retrieving cards: ' . $e->getMessage();
    $cards = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Cards - Virtual ID System</title>
      <link rel="icon" href="../favicon.png" type="image/x-icon">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dee2e6 0%, #adb5bd 50%, #6c757d 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .nav-buttons {
            text-align: center;
            margin-bottom: 30px;
        }

        .nav-btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-bar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            margin: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #0056b3;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card-item {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        .card-type {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card-id {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card-images {
            display: flex;
            padding: 15px;
            gap: 10px;
        }

        .card-thumbnail {
            flex: 1;
            text-align: center;
        }

        .card-thumbnail img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        .card-thumbnail .no-image {
            width: 100%;
            height: 80px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 0.8rem;
        }

        .card-thumbnail-label {
            font-size: 0.7rem;
            color: #666;
            margin-top: 5px;
            font-weight: 500;
        }

        .card-details {
            padding: 15px 20px;
            border-top: 1px solid #e9ecef;
        }

        .card-field {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .card-field:last-child {
            margin-bottom: 0;
        }

        .field-name {
            color: #666;
            font-weight: 500;
        }

        .field-value {
            color: #333;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-word;
        }

        .card-footer {
            background: #f8f9fa;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #666;
        }

        .card-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .action-btn.view {
            background: #667eea;
            color: white;
        }

        .action-btn.download {
            background: #28a745;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .no-cards {
            text-align: center;
            background: white;
            border-radius: 15px;
            padding: 60px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .no-cards i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .no-cards h3 {
            color: #666;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .no-cards p {
            color: #999;
            margin-bottom: 30px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            
            .nav-btn {
                margin: 0;
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-id-card"></i> All Generated Cards</h1>
            <p>View and manage all your virtual ID cards</p>
        </header>

        <div class="nav-buttons">
            <a href="card_creation_image.php" class="nav-btn">
                <i class="fas fa-plus"></i> Create New Card
            </a>
            <a href="../admin/dashboard.php" class="nav-btn">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-number"><?php echo count($cards); ?></span>
                <div class="stat-label">Total Cards</div>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo count(array_unique(array_column($cards, 'card_type'))); ?></span>
                <div class="stat-label">Card Types</div>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo count($cards) > 0 ? date('M j', strtotime($cards[0]['created_at'])) : 'N/A'; ?></span>
                <div class="stat-label">Latest Card</div>
            </div>
        </div>

        <?php if (empty($cards)): ?>
            <div class="no-cards">
                <i class="fas fa-id-card"></i>
                <h3>No Cards Found</h3>
                <p>You haven't created any cards yet. Start by creating your first virtual ID card!</p>
                <a href="card_creation_image.php" class="nav-btn">
                    <i class="fas fa-plus"></i> Create Your First Card
                </a>
            </div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($cards as $card): ?>
                    <div class="card-item" onclick="viewCard('<?php echo htmlspecialchars($card['unique_id']); ?>')">
                        <div class="card-header">
                            <div class="card-type">
                                <?php 
                                // Priority 1: Use stored dropdown card type name (this should match the dropdown exactly)
                                if (!empty($card['dropdown_card_type_name'])) {
                                    echo htmlspecialchars($card['dropdown_card_type_name']);
                                } else {
                                    // Priority 2: Use display name (person/animal name) if available
                                    if (!empty($card['display_name'])) {
                                        echo htmlspecialchars($card['display_name']);
                                    } else {
                                        // Priority 3: Fallback to mapping based on card type for existing cards
                                        $cardType = $card['card_type'];
                                        
                                        // Map card types to dropdown names (matching the dropdown options exactly)
                                        $cardNameMap = [
                                            'combo_dog' => 'Service Dog (Blue) + Service Dog Handler',
                                            'combo_red_dog' => 'Service Dog (Red) + Service Dog Handler',
                                            'combo_emotional_dog' => 'Emotional Dog + Emotional Support Dog',
                                            'combo_emotional_cat' => 'Emotional Support Cat + Cat Handler',
                                            'child_identification' => 'Child Identification Card (Blue)',
                                            'child_identification_red' => 'Child Identification Card (Red)',
                                            'autism_card_infinity' => 'Autism Card (Infinity Sign)',
                                            'autism_card_puzzle' => 'Autism Card (Puzzle Piece)',
                                            'emergency_id_card' => 'Emergency ID Card',
                                            // Legacy single card types
                                            'blue_dog' => 'Service Dog (Blue)',
                                            'red_dog' => 'Service Dog (Red)',
                                            'emotional_dog' => 'Emotional Dog',
                                            'blue_cat' => 'Emotional Support Cat',
                                            'service_dog_handler' => 'Service Dog Handler',
                                            'service_dog_handler_red' => 'Service Dog Handler',
                                            'emotional_support_dog' => 'Emotional Support Dog',
                                            'emotional_cat_handler' => 'Cat Handler'
                                        ];
                                        
                                        $displayName = isset($cardNameMap[$cardType]) ? $cardNameMap[$cardType] : ucwords(str_replace('_', ' ', $cardType));
                                        echo htmlspecialchars($displayName);
                                    }
                                }
                                ?>
                            </div>
                            <div class="card-id">ID: <?php echo htmlspecialchars($card['unique_id']); ?></div>
                        </div>
                        
                        <div class="card-images">
                            <div class="card-thumbnail">
                                <?php if (isset($card['front_thumbnail']) && file_exists($card['front_thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($card['front_thumbnail']); ?>" alt="Front">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                                <div class="card-thumbnail-label">Front</div>
                            </div>
                            <div class="card-thumbnail">
                                <?php if (isset($card['back_thumbnail']) && file_exists($card['back_thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($card['back_thumbnail']); ?>" alt="Back">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                                <div class="card-thumbnail-label">Back</div>
                            </div>
                        </div>
                        
                        <div class="card-details">
                            <?php 
                            $displayFields = array_slice($card['parsed_fields'], 0, 3, true);
                            foreach ($displayFields as $fieldName => $fieldValue): 
                                if (!empty($fieldValue)):
                            ?>
                                <div class="card-field">
                                    <span class="field-name"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $fieldName))); ?>:</span>
                                    <span class="field-value"><?php echo htmlspecialchars($fieldValue); ?></span>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        
                        <div class="card-footer">
                            <div class="card-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M j, Y', strtotime($card['created_at'])); ?>
                            </div>
                            <div class="card-actions" onclick="event.stopPropagation();">
                                <a href="view_card.php?id=<?php echo htmlspecialchars($card['unique_id']); ?>" class="action-btn view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?php echo htmlspecialchars($card['front_image_path']); ?>" download class="action-btn download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function viewCard(cardId) {
            window.open('view_card.php?id=' + cardId, '_blank');
        }
    </script>
</body>
</html>


