<?php
require_once '../Middleware/Authentication.php';

$auth = new Authentication ;


require_once '../config/database.php';
require_once '../New_cards/config.php';

// Check if admin is logged in


// Get statistics from New Cards database
$stmt = $pdo->query("SELECT COUNT(*) as total_cards FROM cards");
$total_cards = $stmt->fetch()['total_cards'];

$stmt = $pdo->query("SELECT COUNT(*) as active_cards FROM cards WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)");
$active_cards = $stmt->fetch()['active_cards'];

$stmt = $pdo->query("SELECT COUNT(*) as expired_cards FROM cards WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)");
$expired_cards = $stmt->fetch()['expired_cards'];

$stmt = $pdo->query("SELECT card_type, COUNT(*) as count FROM cards GROUP BY card_type");
$card_types = $stmt->fetchAll();

// Get recent cards
$stmt = $pdo->query("
    SELECT id, unique_id, card_type, created_at, card_data 
    FROM cards 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recent_cards = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ShieldID</title>
  	<link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding-top: 0 !important;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            margin-top: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?php include 'sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content" style="padding: 15px;">
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-4">
                        <h1 class="h2">Dashboard</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary me-3">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $total_cards ?></h3>
                                    <p class="text-muted mb-0">Total Cards</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success me-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $active_cards ?></h3>
                                    <p class="text-muted mb-0">Active Cards</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $expired_cards ?></h3>
                                    <p class="text-muted mb-0">Expired Cards</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stat-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info me-3">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= date('d') ?></h3>
                                    <p class="text-muted mb-0">Today's Date</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Card Types Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Card Type Distribution</h5>
                            </div>
                            <div class="card-body">
                                <?php 
                                // Group card types by simplified names
                                $grouped_types = [];
                                $type_mapping = [
                                    'combo_dog' => 'Service dog',
                                    'combo_red_dog' => 'Service dog',
                                    'combo_emotional_dog' => 'Emotional dog',
                                    'combo_emotional_cat' => 'Service cat',
                                    'child_identification' => 'Child id',
                                    'child_identification_red' => 'Child id',
                                    'autism_card_infinity' => 'Autism id',
                                    'autism_card_puzzle' => 'Autism id',
                                    'autism_awareness' => 'Autism id',
                                    'service_dog' => 'Service dog',
                                    'emotional_support' => 'Emotional dog',
                                    'emergency_id' => 'Emergency id',
                                    'blue_dog' => 'Service dog',
                                    'red_dog' => 'Service dog',
                                    'service_dog_handler' => 'Service dog',
                                    'service_dog_handler_red' => 'Service dog',
                                    'emotional_support_dog' => 'Emotional dog',
                                    'emergency_id_card' => 'Emergency id',
                                    'blue_cat' => 'Service cat',
                                    'emotional_cat_handler' => 'Service cat'
                                ];
                                
                                // Group counts by simplified type names
                                foreach ($card_types as $type) {
                                    $simplified_type = $type_mapping[$type['card_type']] ?? ucfirst(str_replace('_', ' ', $type['card_type']));
                                    if (!isset($grouped_types[$simplified_type])) {
                                        $grouped_types[$simplified_type] = 0;
                                    }
                                    $grouped_types[$simplified_type] += $type['count'];
                                }
                                
                                // Display grouped types
                                foreach ($grouped_types as $display_type => $count): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span><?= $display_type ?></span>
                                        <span class="badge bg-primary"><?= $count ?></span>
                                    </div>
                                    <div class="progress mb-3" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= $total_cards > 0 ? ($count / $total_cards) * 100 : 0 ?>%"></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Cards -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Recent Cards</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_cards)): ?>
                                    <p class="text-muted text-center">No cards created yet</p>
                                <?php else: ?>
                                    <?php 
                                    // Group cards by unique_id to combine related cards
                                    $grouped_cards = [];
                                    foreach ($recent_cards as $card) {
                                        $unique_id = $card['unique_id'];
                                        if (!isset($grouped_cards[$unique_id])) {
                                            $grouped_cards[$unique_id] = [];
                                        }
                                        $grouped_cards[$unique_id][] = $card;
                                    }
                                    
                                    // Map card types to simplified names
                                    $type_mapping = [
                                        'combo_dog' => 'Service Dog (Blue)',
                                        'combo_red_dog' => 'Service Dog (Red)',
                                        'combo_emotional_dog' => 'Emotional Dog',
                                        'combo_emotional_cat' => 'Emotional Support Cat',
                                        'child_identification' => 'Child identification card (Blue)',
                                        'child_identification_red' => 'Child identification card (red)',
                                        'autism_card_infinity' => 'Autism card (infinity)',
                                        'autism_card_puzzle' => 'Autism card (puzzle piece)',
                                        'autism_awareness' => 'Autism card (puzzle piece)',
                                        'service_dog' => 'Service Dog (Blue)',
                                        'emotional_support' => 'Emotional Dog',
                                        'emergency_id' => 'Emergency ID',
                                        'blue_dog' => 'Service Dog (Blue)',
                                        'red_dog' => 'Service Dog (Red)',
                                        'service_dog_handler' => 'Service Dog Handler',
                                        'service_dog_handler_red' => 'Service Dog Handler',
                                        'emotional_support_dog' => 'Emotional Support Dog',
                                        'emergency_id_card' => 'Emergency ID',
                                        'blue_cat' => 'Emotional Support Cat',
                                        'emotional_cat_handler' => 'Cat Handler'
                                    ];
                                    
                                    // Display grouped cards
                                    foreach ($grouped_cards as $unique_id => $cards): 
                                        $first_card = $cards[0];
                                        $card_data = json_decode($first_card['card_data'], true);
                                        
                                        // Extract card name with priority order
                                        $card_name = 'Unknown';
                                        if (isset($card_data['displayName']) && !empty($card_data['displayName'])) {
                                            $card_name = $card_data['displayName'];
                                        } elseif (isset($card_data['animalName']) && !empty($card_data['animalName'])) {
                                            $card_name = $card_data['animalName'];
                                        } elseif (isset($card_data['handlerName']) && !empty($card_data['handlerName'])) {
                                            $card_name = $card_data['handlerName'];
                                        } elseif (isset($card_data['childName']) && !empty($card_data['childName'])) {
                                            $card_name = $card_data['childName'];
                                        } elseif (isset($card_data['autismName']) && !empty($card_data['autismName'])) {
                                            $card_name = $card_data['autismName'];
                                        } elseif (isset($card_data['patientName']) && !empty($card_data['patientName'])) {
                                            $card_name = $card_data['patientName'];
                                        } elseif (isset($card_data['emergencyContactName']) && !empty($card_data['emergencyContactName'])) {
                                            $card_name = $card_data['emergencyContactName'];
                                        } elseif (isset($card_data['name']) && !empty($card_data['name'])) {
                                            $card_name = $card_data['name'];
                                        }
                                        
                                        // Combine card types with +
                                        $display_types = [];
                                        foreach ($cards as $card) {
                                            $display_type = $type_mapping[$card['card_type']] ?? ucfirst(str_replace('_', ' ', $card['card_type']));
                                            if (!in_array($display_type, $display_types)) {
                                                $display_types[] = $display_type;
                                            }
                                        }
                                        $combined_type = implode(' + ', $display_types);
                                        
                                        $status = (strtotime($first_card['created_at']) > strtotime('-1 year')) ? 'active' : 'expired';
                                    ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                            <div>
                                                <strong><?= htmlspecialchars($card_name) ?></strong><br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($unique_id) ?> • 
                                                    <?= $combined_type ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-<?= $status == 'active' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Database Configuration</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Database:</strong> <?= htmlspecialchars($dbname) ?></li>
                                            <li><strong>Host:</strong> <?= htmlspecialchars($host) ?></li>
                                            <li><strong>Base URL:</strong> <?= htmlspecialchars($base_url) ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Card System Features</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success me-2"></i>QR Code Generation</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Multiple Card Types</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Image Processing</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Thumbnail Creation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <a href="../New_cards/card_creation_image.php" class="btn btn-outline-primary w-100 p-4">
                                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                            Create New Card
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="cards.php" class="btn btn-outline-info w-100 p-4">
                                            <i class="fas fa-list fa-2x mb-2"></i><br>
                                            Manage Cards
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="../New_cards/view_all_cards.php" class="btn btn-outline-success w-100 p-4" target="_blank">
                                            <i class="fas fa-eye fa-2x mb-2"></i><br>
                                            View All Cards
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="../New_cards/card_creation_image.php" class="btn btn-outline-warning w-100 p-4" target="_blank">
                                            <i class="fas fa-palette fa-2x mb-2"></i><br>
                                            Card Editor
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>