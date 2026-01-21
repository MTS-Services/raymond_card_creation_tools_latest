<?php
require_once '../Middleware/Authentication.php';
require_once 'config.php';

// Check if admin is logged in
new Authentication;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(card_type LIKE ? OR unique_id LIKE ? OR (fields IS NOT NULL AND fields != '' AND (JSON_EXTRACT(fields, '$.name') LIKE ? OR JSON_EXTRACT(fields, '$.handlerName') LIKE ?)))";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if ($type_filter !== 'all') {
    $where_conditions[] = "card_type = ?";
    $params[] = $type_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) FROM cards $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_cards = $count_stmt->fetchColumn();

// Get cards with pagination
$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$sql = "SELECT * FROM cards $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get card types for filter
$types_stmt = $pdo->query("SELECT DISTINCT card_type FROM cards ORDER BY card_type");
$card_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);

// Function to get card status (simplified - all active for now)
function getCardStatus($card) {
    return 'Active';
}

// Function to get card type display name
function getCardTypeDisplay($card_type) {
    $types = [
        'blue_dog' => 'Service Dog (Blue)',
        'red_dog' => 'Service Dog (Red)',
        'emotional_dog' => 'Emotional Dog',
        'blue_cat' => 'Emotional Cat',
        'child_identification' => 'Child ID (Blue)',
        'child_identification_red' => 'Child ID (Red)',
        'autism_card' => 'Autism Card',
        'emergency_id_card' => 'Emergency ID'
    ];
    return $types[$card_type] ?? ucwords(str_replace('_', ' ', $card_type));
}

// Function to get card type color
function getCardTypeColor($card_type) {
    $colors = [
        'blue_dog' => 'success',
        'red_dog' => 'success',
        'emotional_dog' => 'success',
        'blue_cat' => 'info',
        'child_identification' => 'primary',
        'child_identification_red' => 'primary',
        'autism_card' => 'warning',
        'emergency_id_card' => 'danger'
    ];
    return $colors[$card_type] ?? 'secondary';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cards - Virtual ID Cards</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
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
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: rgba(255,255,255,0.5);
        }

        .menu-item.active {
            background: rgba(255,255,255,0.2);
            border-left-color: white;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .submenu {
            margin-left: 20px;
        }

        .submenu .menu-item {
            padding: 12px 25px;
            font-size: 14px;
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
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .user-details h4 {
            font-size: 14px;
            margin-bottom: 2px;
        }

        .user-details p {
            font-size: 12px;
            opacity: 0.8;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            color: #ffc107;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .logout-btn:hover {
            color: #ffcd39;
        }

        .logout-btn i {
            margin-right: 8px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 0;
        }

        .top-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .content-area {
            padding: 30px;
        }

        /* Search and Filter Section */
        .search-filter-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .search-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .search-group {
            flex: 1;
            min-width: 200px;
        }

        .search-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* Cards Table */
        .cards-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .table-count {
            color: #6c757d;
            font-size: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #e9ecef;
        }

        .table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .card-number {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #333;
        }

        .card-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success { background: #d4edda; color: #155724; }
        .badge-primary { background: #cce7ff; color: #004085; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-view {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-edit {
            background: #fff3e0;
            color: #f57c00;
        }

        .btn-delete {
            background: #ffebee;
            color: #d32f2f;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            color: #667eea;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .current {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Responsive */
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

            .search-row {
                flex-direction: column;
            }

            .search-group {
                min-width: 100%;
            }

            .table {
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 10px 15px;
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
                <a href="manage_cards.php" class="menu-item active">
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
            <h1 class="page-title">Manage ID Cards</h1>
            <div class="header-actions">
                <a href="card_creation_image.php" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Create New Card
                </a>
            </div>
        </div>

        <div class="content-area">
            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <form method="GET" class="search-row">
                    <div class="search-group">
                        <label for="search">Search</label>
                        <input type="text" id="search" name="search" class="search-input" 
                               placeholder="Search by name, card number, or email" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="search-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" class="filter-select">
                            <option value="all">All Types</option>
                            <?php foreach ($card_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" 
                                        <?php echo $type_filter === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(getCardTypeDisplay($type)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="search-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </form>
            </div>

            <!-- Cards Table -->
            <div class="cards-table-container">
                <div class="table-header">
                    <div class="table-title">ID Cards</div>
                    <div class="table-count">(<?php echo number_format($total_cards); ?> total)</div>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Card Number</th>
                            <th>Full Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cards)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                    <br>No cards found matching your criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cards as $card): ?>
                                <?php 
                                $fields = [];
                                if (!empty($card['fields'])) {
                                    $fields = json_decode($card['fields'], true) ?? [];
                                }
                                $name = $fields['name'] ?? $fields['handlerName'] ?? 'N/A';
                                $expires_date = date('M j, Y', strtotime($card['created_at'] . ' +1 year'));
                                ?>
                                <tr>
                                    <td>
                                        <div class="card-number"><?php echo htmlspecialchars($card['unique_id']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                    <td>
                                        <span class="card-type-badge badge-<?php echo getCardTypeColor($card['card_type']); ?>">
                                            <?php echo htmlspecialchars(getCardTypeDisplay($card['card_type'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge"><?php echo getCardStatus($card); ?></span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($card['created_at'])); ?></td>
                                    <td><?php echo $expires_date; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_card.php?id=<?php echo htmlspecialchars($card['unique_id']); ?>" 
                                               class="btn-action btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="card_creation_image.php?edit=<?php echo htmlspecialchars($card['unique_id']); ?>" 
                                               class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo htmlspecialchars($card['unique_id']); ?>" 
                                               class="btn-action btn-delete" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this card?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_cards > $per_page): ?>
                <div class="pagination">
                    <?php
                    $total_pages = ceil($total_cards / $per_page);
                    $current_page = $page;
                    
                    // Previous page
                    if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    // Page numbers
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type_filter); ?>&status=<?php echo urlencode($status_filter); ?>"
                           class="<?php echo $i == $current_page ? 'current' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php
                    // Next page
                    if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Handle delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('.btn-delete');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this card? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>
