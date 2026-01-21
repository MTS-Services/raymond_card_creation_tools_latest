<?php
require_once '../Middleware/Authentication.php';
new Authentication;
// Include database configuration
require_once 'config.php';

try {
    // Get total cards count
    $stmt = $pdo->query("SELECT COUNT(*) as total_cards FROM cards");
    $total_cards = $stmt->fetch()['total_cards'];
    
    // Get cards by type
    $stmt = $pdo->query("SELECT card_type, COUNT(*) as count FROM cards GROUP BY card_type ORDER BY count DESC");
    $cards_by_type = $stmt->fetchAll();
    
    // Get cards by month (last 12 months)
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM cards 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $cards_by_month = $stmt->fetchAll();
    
    // Get recent activity (last 30 days)
    $stmt = $pdo->query("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as count
        FROM cards 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ");
    $recent_activity = $stmt->fetchAll();
    
    // Get system start date
    $stmt = $pdo->query("SELECT MIN(created_at) as start_date FROM cards");
    $start_date_result = $stmt->fetch();
    $start_date = $start_date_result['start_date'] ?? date('Y-m-d');
    
    // Calculate days since start
    $days_since_start = $start_date ? (strtotime('now') - strtotime($start_date)) / (60 * 60 * 24) : 0;
    $avg_cards_per_day = $days_since_start > 0 ? round($total_cards / $days_since_start, 2) : 0;
    
    // Get most popular card type
    $most_popular_type = !empty($cards_by_type) ? $cards_by_type[0]['card_type'] : 'N/A';
    $most_popular_count = !empty($cards_by_type) ? $cards_by_type[0]['count'] : 0;
    
} catch (Exception $e) {
    $total_cards = 0;
    $cards_by_type = [];
    $cards_by_month = [];
    $recent_activity = [];
    $start_date = date('Y-m-d');
    $days_since_start = 0;
    $avg_cards_per_day = 0;
    $most_popular_type = 'N/A';
    $most_popular_count = 0;
    $error = 'Error retrieving statistics: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Statistics - Virtual ID Cards</title>
  <link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-text {
            font-size: 14px;
            opacity: 0.9;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card h3 {
            font-size: 36px;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .chart-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-data i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .card-type-badge {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .container {
                padding: 20px 15px;
            }

            .chart-wrapper {
                height: 300px;
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
    <div class="header">
        <div class="header-content">
            <h1><i class="fas fa-chart-bar"></i> Card Statistics</h1>
            <div class="header-info">
                <div class="welcome-text">
                    Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </div>
                <a href="dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Key Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo number_format($total_cards); ?></h3>
                <p>Total Cards Created</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $avg_cards_per_day; ?></h3>
                <p>Average Cards Per Day</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($days_since_start); ?></h3>
                <p>Days Since Start</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $most_popular_count; ?></h3>
                <p>Most Popular Type</p>
            </div>
        </div>

        <!-- Cards by Type Chart -->
        <div class="chart-container">
            <h3 class="chart-title">
                <i class="fas fa-pie-chart"></i> Cards by Type
            </h3>
            <?php if (!empty($cards_by_type)): ?>
                <div class="chart-wrapper">
                    <canvas id="cardsByTypeChart"></canvas>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-chart-pie"></i>
                    <p>No card data available for chart display</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cards by Month Chart -->
        <div class="chart-container">
            <h3 class="chart-title">
                <i class="fas fa-chart-line"></i> Cards Created Over Time (Last 12 Months)
            </h3>
            <?php if (!empty($cards_by_month)): ?>
                <div class="chart-wrapper">
                    <canvas id="cardsByMonthChart"></canvas>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-chart-line"></i>
                    <p>No monthly data available for chart display</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Detailed Statistics Table -->
        <div class="table-container">
            <h3 class="chart-title">
                <i class="fas fa-table"></i> Detailed Statistics
            </h3>
            <?php if (!empty($cards_by_type)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Card Type</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cards_by_type as $type): ?>
                            <tr>
                                <td>
                                    <span class="card-type-badge">
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $type['card_type']))); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($type['count']); ?></td>
                                <td><?php echo $total_cards > 0 ? round(($type['count'] / $total_cards) * 100, 1) : 0; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-table"></i>
                    <p>No card data available for table display</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Cards by Type Chart
        <?php if (!empty($cards_by_type)): ?>
        const cardsByTypeCtx = document.getElementById('cardsByTypeChart').getContext('2d');
        new Chart(cardsByTypeCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php foreach ($cards_by_type as $type): ?>
                        '<?php echo addslashes(ucwords(str_replace('_', ' ', $type['card_type']))); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    data: [
                        <?php foreach ($cards_by_type as $type): ?>
                            <?php echo $type['count']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#56ab2f',
                        '#a8e6cf',
                        '#f39c12',
                        '#f7dc6f',
                        '#e74c3c',
                        '#f1948a',
                        '#9b59b6',
                        '#bb8fce'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Cards by Month Chart
        <?php if (!empty($cards_by_month)): ?>
        const cardsByMonthCtx = document.getElementById('cardsByMonthChart').getContext('2d');
        new Chart(cardsByMonthCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($cards_by_month as $month): ?>
                        '<?php echo date('M Y', strtotime($month['month'] . '-01')); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Cards Created',
                    data: [
                        <?php foreach ($cards_by_month as $month): ?>
                            <?php echo $month['count']; ?>,
                        <?php endforeach; ?>
                    ],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Animate stat numbers
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-card h3');
            statNumbers.forEach(stat => {
                const finalValue = parseFloat(stat.textContent.replace(/,/g, ''));
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
        });
    </script>
</body>
</html>






