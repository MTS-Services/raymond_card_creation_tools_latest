<?php
require_once '../Middleware/Authentication.php';
require_once '../config/database.php';

$auth = new Authentication;

// Handle delete action
if (isset($_GET['delete']) && isset($_SESSION['admin_id'])) {
    $card_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->execute([$card_id]);
    header('Location: cards.php?deleted=1');
    exit;
}

// Get search parameters
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(unique_id LIKE ? OR card_type LIKE ? OR card_data LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type_filter) {
    $where_conditions[] = "card_type = ?";
    $params[] = $type_filter;
}

if ($status_filter) {
    if ($status_filter == 'active') {
        $where_conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    } elseif ($status_filter == 'expired') {
        $where_conditions[] = "created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    }
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

// Get cards with pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM cards $where_clause");
$count_stmt->execute($params);
$total_cards = $count_stmt->fetchColumn();
$total_pages = ceil($total_cards / $per_page);

$stmt = $pdo->prepare("
    SELECT id, unique_id, card_type, created_at, card_data
    FROM cards 
    $where_clause
    ORDER BY created_at DESC 
    LIMIT $per_page OFFSET $offset
");
$stmt->execute($params);
$cards = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cards - ShieldID Admin</title>
  	<link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0;
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding-top: 0 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content" style="padding: 15px;">
                <div class="pb-2 mb-3">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Manage ID Cards</h1>
                        <a href="../New_cards/card_creation_image.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Card
                        </a>
                    </div>
                </div>
                
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>Card deleted successfully!
                    </div>
                <?php endif; ?>
                
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="Search by Card number, name or card type" value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    <option value="child_id" <?= $type_filter == 'child_id' ? 'selected' : '' ?>>Child ID</option>
                                    <option value="service_dog" <?= $type_filter == 'service_dog' ? 'selected' : '' ?>>Service Dog</option>
                                    <option value="emergency_id" <?= $type_filter == 'emergency_id' ? 'selected' : '' ?>>Emergency ID</option>
                                    <option value="autism_awareness" <?= $type_filter == 'autism_awareness' ? 'selected' : '' ?>>Autism Awareness</option>
                                    <option value="emotional_support" <?= $type_filter == 'emotional_support' ? 'selected' : '' ?>>Emotional Support</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="expired" <?= $status_filter == 'expired' ? 'selected' : '' ?>>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Cards Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ID Cards (<?= $total_cards ?> total)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Card Number</th>
                                        <th>Full Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cards)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                No cards found. <a href="../New_cards/card_creation_image.php">Create your first card</a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($cards as $card): ?>
                                            <?php 
                                            $card_data = json_decode($card['card_data'], true);
                                            
                                            // Extract meaningful card name based on card type and available data
                                            $card_name = 'Unknown';
                                            
                                            // First check for displayName (set during card creation)
                                            if (isset($card_data['displayName']) && !empty($card_data['displayName'])) {
                                                $card_name = $card_data['displayName'];
                                            }
                                            // Then check fields array for specific names
                                            elseif (isset($card_data['fields']) && is_array($card_data['fields'])) {
                                                $fields = $card_data['fields'];
                                                if (isset($fields['animalName']) && !empty($fields['animalName'])) {
                                                    $card_name = $fields['animalName'];
                                                } elseif (isset($fields['handlerName']) && !empty($fields['handlerName'])) {
                                                    $card_name = $fields['handlerName'];
                                                } elseif (isset($fields['childName']) && !empty($fields['childName'])) {
                                                    $card_name = $fields['childName'];
                                                } elseif (isset($fields['autismName']) && !empty($fields['autismName'])) {
                                                    $card_name = $fields['autismName'];
                                                } elseif (isset($fields['patientName']) && !empty($fields['patientName'])) {
                                                    $card_name = $fields['patientName'];
                                                } elseif (isset($fields['emergencyContactName']) && !empty($fields['emergencyContactName'])) {
                                                    $card_name = $fields['emergencyContactName'];
                                                } elseif (isset($fields['name']) && !empty($fields['name'])) {
                                                    $card_name = $fields['name'];
                                                }
                                            }
                                            // Fallback to direct card_data fields
                                            elseif (isset($card_data['name']) && !empty($card_data['name'])) {
                                                $card_name = $card_data['name'];
                                            }
                                            
                                            $status = (strtotime($card['created_at']) > strtotime('-1 year')) ? 'active' : 'expired';
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($card['unique_id']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($card_name) ?></td>
                                                <td>
                                                    <?php
                                                    // Map card types to simplified names
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
                                                        'emergency_id' => 'Emergency id'
                                                    ];
                                                    
                                                    $display_type = $type_mapping[$card['card_type']] ?? ucfirst(str_replace('_', ' ', $card['card_type']));
                                                    
                                                    // All badges will be grey
                                                    $badge_color = 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?= $badge_color ?>">
                                                        <?= $display_type ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $status == 'active' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($card['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="../New_cards/view_card.php?id=<?= $card['unique_id'] ?>" class="btn btn-outline-primary" title="View" target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="../New_cards/card_creation_image.php?edit=<?= $card['unique_id'] ?>" class="btn btn-outline-secondary" title="Edit" target="_blank">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?delete=<?= $card['id'] ?>" class="btn btn-outline-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this card?')" title="Delete">
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
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&status=<?= urlencode($status_filter) ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&status=<?= urlencode($status_filter) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type_filter) ?>&status=<?= urlencode($status_filter) ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>