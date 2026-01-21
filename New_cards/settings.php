<?php
require_once '../Middleware/Authentication.php';

$auth = new Authentication;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../admin/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Virtual ID Cards</title>
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

        .content-area {
            padding: 30px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }

        .settings-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border: 1px solid #e1e5e9;
        }

        .settings-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        .info-box {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box i {
            color: #0c5460;
            margin-right: 8px;
        }

        .info-box p {
            color: #0c5460;
            font-size: 14px;
            margin: 0;
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

            .settings-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .content-area {
                padding: 20px 15px;
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
            <a href="dashboard.php" class="menu-item">
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

            <a href="settings.php" class="menu-item active">
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
            <h1 class="page-title">Settings</h1>
        </div>

        <div class="content-area">
            <div class="settings-grid">
                <!-- System Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-cog"></i> System Settings</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>Configure general system settings and preferences.</p>
                    </div>

                    <div class="form-group">
                        <label for="system_name">System Name</label>
                        <input type="text" id="system_name" class="form-control" value="ShieldID Admin" readonly>
                    </div>

                    <div class="form-group">
                        <label for="max_cards">Maximum Cards Per User</label>
                        <input type="number" id="max_cards" class="form-control" value="100" min="1" max="1000">
                    </div>

                    <div class="form-group">
                        <label for="card_expiry">Card Expiry (Days)</label>
                        <input type="number" id="card_expiry" class="form-control" value="365" min="30" max="3650">
                    </div>

                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>

                <!-- Security Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-lock"></i>
                        <p>Manage security settings and access controls.</p>
                    </div>

                    <div class="form-group">
                        <label for="session_timeout">Session Timeout (Minutes)</label>
                        <input type="number" id="session_timeout" class="form-control" value="30" min="5" max="480">
                    </div>

                    <div class="form-group">
                        <label for="max_login_attempts">Max Login Attempts</label>
                        <input type="number" id="max_login_attempts" class="form-control" value="5" min="3" max="10">
                    </div>

                    <div class="form-group">
                        <label for="password_policy">Password Policy</label>
                        <select id="password_policy" class="form-control">
                            <option value="basic">Basic (6+ characters)</option>
                            <option value="medium" selected>Medium (8+ chars, numbers)</option>
                            <option value="strong">Strong (12+ chars, mixed case, symbols)</option>
                        </select>
                    </div>

                    <button class="btn btn-success">
                        <i class="fas fa-save"></i> Update Security
                    </button>
                </div>

                <!-- Database Settings -->
                <div class="settings-card">
                    <h3><i class="fas fa-database"></i> Database Settings</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-database"></i>
                        <p>Database configuration and maintenance options.</p>
                    </div>

                    <div class="form-group">
                        <label for="backup_frequency">Backup Frequency</label>
                        <select id="backup_frequency" class="form-control">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="retention_days">Data Retention (Days)</label>
                        <input type="number" id="retention_days" class="form-control" value="365" min="30" max="3650">
                    </div>

                    <button class="btn btn-warning">
                        <i class="fas fa-download"></i> Backup Now
                    </button>
                </div>

                <!-- Maintenance -->
                <div class="settings-card">
                    <h3><i class="fas fa-tools"></i> Maintenance</h3>
                    
                    <div class="info-box">
                        <i class="fas fa-wrench"></i>
                        <p>System maintenance and cleanup operations.</p>
                    </div>

                    <div class="form-group">
                        <label>System Status</label>
                        <div style="padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">
                            <i class="fas fa-check-circle"></i> All systems operational
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Storage Usage</label>
                        <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; color: #856404;">
                            <i class="fas fa-hdd"></i> 2.3 GB used of 10 GB available
                        </div>
                    </div>

                    <button class="btn btn-danger">
                        <i class="fas fa-trash"></i> Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const forms = document.querySelectorAll('.settings-card');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('.form-control');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            this.style.borderColor = '#e74c3c';
                        } else {
                            this.style.borderColor = '#27ae60';
                        }
                    });
                });
            });

            // Button click effects
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Add loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    this.disabled = true;
                    
                    // Simulate processing
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                        
                        // Show success message
                        const successMsg = document.createElement('div');
                        successMsg.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: #27ae60;
                            color: white;
                            padding: 15px 20px;
                            border-radius: 5px;
                            z-index: 10000;
                            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                        `;
                        successMsg.innerHTML = '<i class="fas fa-check"></i> Settings saved successfully!';
                        document.body.appendChild(successMsg);
                        
                        setTimeout(() => {
                            successMsg.remove();
                        }, 3000);
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
