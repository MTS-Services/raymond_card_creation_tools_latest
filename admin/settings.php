<?php
require_once '../Middleware/Authentication.php';
require_once '../config/database.php';
require_once '../New_cards/config.php';

$auth = new Authentication;

// Handle form submission
if ($_POST) {
    // Here you can add settings update logic if needed
    $message = "Settings updated successfully!";
}

// Get current system information
$system_info = [
    'database_name' => $dbname,
    'database_host' => $host,
    'base_url' => $base_url,
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
];

// Get database statistics
$db_stats = [];
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_cards FROM cards");
    $db_stats['total_cards'] = $stmt->fetch()['total_cards'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_admins FROM admins");
    $db_stats['total_admins'] = $stmt->fetch()['total_admins'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as card_types FROM (SELECT DISTINCT card_type FROM cards) as types");
    $db_stats['card_types'] = $stmt->fetch()['card_types'];
} catch (Exception $e) {
    $db_stats = ['error' => $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - ShieldID Admin</title>
  	<link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
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
        
        .settings-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .settings-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #6c757d;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .stat-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content" style="padding: 15px;">
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-4">
                        <h1 class="h2"><i class="fas fa-cog me-2"></i>System Settings</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Database Configuration -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">Database Name</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['database_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Database Host</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['database_host']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Base URL</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['base_url']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Connection Status</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                    </div>
                </div>
                
                <!-- System Information -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">PHP Version</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['php_version']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Server Software</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['server_software']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Document Root</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['document_root']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Current Time</span>
                            <span class="info-value"><?= date('Y-m-d H:i:s') ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Database Statistics -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Database Statistics</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($db_stats['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>Error: <?= htmlspecialchars($db_stats['error']) ?>
                            </div>
                        <?php else: ?>
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="stat-badge">
                                        <i class="fas fa-id-card me-2"></i>
                                        <?= $db_stats['total_cards'] ?> Cards
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-badge">
                                        <i class="fas fa-users me-2"></i>
                                        <?= $db_stats['total_admins'] ?> Admins
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-badge">
                                        <i class="fas fa-layer-group me-2"></i>
                                        <?= $db_stats['card_types'] ?> Types
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- New Cards System Integration -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i>New Cards System Integration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Available Features</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>QR Code Generation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Multiple Card Types</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Image Processing</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Thumbnail Creation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Card Gallery</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Advanced Editor</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Quick Access</h6>
                                <div class="d-grid gap-2">
                                    <a href="../New_cards/card_creation_image.php" class="btn btn-outline-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Create New Card
                                    </a>
                                    <a href="../New_cards/view_all_cards.php" class="btn btn-outline-success" target="_blank">
                                        <i class="fas fa-eye me-2"></i>View All Cards
                                    </a>
                                    <a href="../New_cards/index.html" class="btn btn-outline-warning" target="_blank">
                                        <i class="fas fa-palette me-2"></i>Card Editor
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Actions -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>System Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-outline-info w-100" onclick="refreshStats()">
                                    <i class="fas fa-sync-alt me-2"></i>Refresh Statistics
                                </button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="dashboard.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-outline-warning w-100" onclick="exportSettings()">
                                    <i class="fas fa-download me-2"></i>Export Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshStats() {
            location.reload();
        }
        
        function exportSettings() {
            const settings = {
                database_name: '<?= $system_info['database_name'] ?>',
                database_host: '<?= $system_info['database_host'] ?>',
                base_url: '<?= $system_info['base_url'] ?>',
                php_version: '<?= $system_info['php_version'] ?>',
                server_software: '<?= $system_info['server_software'] ?>',
                export_date: new Date().toISOString()
            };
            
            const dataStr = JSON.stringify(settings, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'shieldid_settings_' + new Date().toISOString().split('T')[0] + '.json';
            link.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>






