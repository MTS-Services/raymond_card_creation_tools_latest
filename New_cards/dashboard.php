<?php
 require_once '../Middleware/Authentication.php';

 new Authentication;

// Include database configuration
require_once 'config.php';

// Get card statistics
try {
    // Count total cards
    $stmt = $pdo->query("SELECT COUNT(*) as total_cards FROM cards");
    $total_cards = $stmt->fetch()['total_cards'];
    
    // Count cards from start date (assuming start date is when first card was created)
    $stmt = $pdo->query("SELECT MIN(created_at) as start_date FROM cards");
    $start_date_result = $stmt->fetch();
    $start_date = $start_date_result['start_date'] ?? date('Y-m-d');
    
    // Count cards since start date
    $stmt = $pdo->prepare("SELECT COUNT(*) as cards_since_start FROM cards WHERE created_at >= ?");
    $stmt->execute([$start_date]);
    $cards_since_start = $stmt->fetch()['cards_since_start'];
    
    // Count cards by type
    $stmt = $pdo->query("SELECT card_type, COUNT(*) as count FROM cards GROUP BY card_type");
    $cards_by_type = $stmt->fetchAll();
    
    // Get recent cards
    $stmt = $pdo->query("SELECT * FROM cards ORDER BY created_at DESC LIMIT 5");
    $recent_cards = $stmt->fetchAll();
    
} catch (Exception $e) {
    $total_cards = 0;
    $cards_since_start = 0;
    $cards_by_type = [];
    $recent_cards = [];
    $start_date = date('Y-m-d');
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Virtual ID Cards</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            width: 80px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sidebar-subtitle {
            font-size: 12px;
            opacity: 0.7;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: block;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #3498db;
        }

        .menu-item.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
            border-left-color: #3498db;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .menu-item span {
            font-size: 14px;
            font-weight: 500;
        }

        .submenu {
            background: rgba(0,0,0,0.1);
            margin-left: 20px;
            border-radius: 8px;
            margin-top: 5px;
            overflow: hidden;
        }

        .submenu .menu-item {
            padding: 12px 20px;
            font-size: 13px;
            border-left: none;
        }

        .submenu .menu-item:hover {
            background: rgba(255,255,255,0.15);
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }

        .user-details h4 {
            font-size: 14px;
            margin-bottom: 2px;
        }

        .user-details p {
            font-size: 12px;
            opacity: 0.7;
        }

        .logout-btn {
            width: 100%;
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
            text-align: center;
            display: block;
        }

        .logout-btn:hover {
            background: rgba(231, 76, 60, 0.3);
            transform: translateY(-1px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        .top-header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 18px;
            color: #7f8c8d;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background: #f8f9fa;
            color: #3498db;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content-area {
            padding: 30px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e1e5e9;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-right: 15px;
        }

        .card-icon.primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .card-icon.success {
            background: linear-gradient(135deg, #56ab2f, #a8e6cf);
        }

        .card-icon.info {
            background: linear-gradient(135deg, #3498db, #85c1e9);
        }

        .card-icon.warning {
            background: linear-gradient(135deg, #f39c12, #f7dc6f);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .card-content {
            margin-bottom: 20px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #56ab2f, #a8e6cf);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #3498db, #85c1e9);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
        }

        .recent-cards {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .recent-cards h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-list {
            list-style: none;
        }

        .card-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .card-item:last-child {
            border-bottom: none;
        }

        .card-info {
            flex: 1;
        }

        .card-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .card-meta {
            font-size: 12px;
            color: #666;
        }

        .card-type {
            background: #f8f9fa;
            color: #667eea;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-area {
                padding: 20px 15px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="logo.png" alt="ShieldID Admin">
            </div>
            <div class="sidebar-title">ShieldID Admin</div>
            <div class="sidebar-subtitle">Virtual ID Cards</div>
        </div>

        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="manage_cards.php" class="menu-item">
                <i class="fas fa-id-card"></i>
                <span>Manage Cards</span>
            </a>
            <div class="submenu">
                <a href="manage_cards.php" class="menu-item">
                    <i class="fas fa-cogs"></i>
                    <span>Manage All Cards</span>
                </a>
                <a href="view_all_cards.php" class="menu-item">
                    <i class="fas fa-list"></i>
                    <span>View All Cards</span>
                </a>
                <a href="card_creation_image.php" class="menu-item">
                    <i class="fas fa-plus"></i>
                    <span>Create New Card</span>
                </a>
            </div>

            <a href="statistics.php" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistics</span>
            </a>

            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h4><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h4>
                    <p>Administrator</p>
                </div>
            </div>
            <a href="?logout=1" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-header">
            <h1 class="page-title">Dashboard</h1>
            <div class="header-actions">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
            </div>
        </div>

        <div class="content-area">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo number_format($total_cards); ?></h3>
                <p>Total Cards Created</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($cards_since_start); ?></h3>
                <p>Cards Since Start</p>
            </div>
            <div class="stat-card">
                <h3><?php echo date('M d, Y', strtotime($start_date)); ?></h3>
                <p>System Start Date</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($cards_by_type); ?></h3>
                <p>Card Types Available</p>
            </div>
        </div>

        <!-- Main Action Cards -->
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon primary">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="card-title">Create New Card</div>
                </div>
                <div class="card-content">
                    <p>Design and create new virtual ID cards with custom information and styling.</p>
                </div>
                <a href="card_creation_image.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Card
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon success">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="card-title">View All Cards</div>
                </div>
                <div class="card-content">
                    <p>Browse and manage all created cards. View, edit, or download existing cards.</p>
                </div>
                <a href="view_all_cards.php" class="btn btn-success">
                    <i class="fas fa-list"></i> View Cards
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="card-title">Card Statistics</div>
                </div>
                <div class="card-content">
                    <p>View detailed statistics and analytics about card usage and creation patterns.</p>
                </div>
                <a href="statistics.php" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> View Statistics
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon warning">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="card-title">System Settings</div>
                </div>
                <div class="card-content">
                    <p>Configure system settings, manage templates, and adjust card creation options.</p>
                </div>
                <a href="settings.php" class="btn btn-warning">
                    <i class="fas fa-tools"></i> Settings
                </a>
            </div>
        </div>

        <!-- Recent Cards Section -->
        <div class="recent-cards">
            <h3><i class="fas fa-clock"></i> Recent Cards</h3>
            <?php if (empty($recent_cards)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No cards have been created yet.</p>
                    <p>Start by creating your first card!</p>
                </div>
            <?php else: ?>
                <ul class="card-list">
                    <?php foreach ($recent_cards as $card): ?>
                        <li class="card-item">
                            <div class="card-info">
                                <div class="card-name">
                                    <?php echo htmlspecialchars($card['fields']['animal_name'] ?? $card['fields']['child_name'] ?? 'Unnamed Card'); ?>
                                </div>
                                <div class="card-meta">
                                    Created: <?php echo date('M d, Y H:i', strtotime($card['created_at'])); ?>
                                </div>
                            </div>
                            <div class="card-type">
                                <?php echo ucwords(str_replace('_', ' ', $card['card_type'])); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat numbers
            const statNumbers = document.querySelectorAll('.stat-number, .stat-card h3');
            statNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
                if (!isNaN(finalValue)) {
                    let currentValue = 0;
                    const increment = finalValue / 50;
                    const timer = setInterval(() => {
                        currentValue += increment;
                        if (currentValue >= finalValue) {
                            stat.textContent = finalValue.toLocaleString();
                            clearInterval(timer);
                        } else {
                            stat.textContent = Math.floor(currentValue).toLocaleString();
                        }
                    }, 30);
                }
            });

            // Add hover effects to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
