<?php
require_once 'config.php';

echo "<h1>Testing View Cards Functionality</h1>";

try {
    // Test database connection
    echo "<h2>Database Connection Test</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM cards");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Database connected successfully. Found {$result['count']} cards in database.</p>";
    
    // Test cards table structure
    echo "<h2>Cards Table Structure</h2>";
    $stmt = $pdo->query("DESCRIBE cards");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test sample cards
    echo "<h2>Sample Cards</h2>";
    $stmt = $pdo->query("SELECT * FROM cards ORDER BY created_at DESC LIMIT 3");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cards)) {
        echo "<p>No cards found in database.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Unique ID</th><th>Card Type</th><th>Created</th><th>Front Image</th><th>Back Image</th></tr>";
        foreach ($cards as $card) {
            echo "<tr>";
            echo "<td>{$card['id']}</td>";
            echo "<td>{$card['unique_id']}</td>";
            echo "<td>{$card['card_type']}</td>";
            echo "<td>{$card['created_at']}</td>";
            echo "<td>" . (file_exists($card['front_image_path']) ? "✅" : "❌") . "</td>";
            echo "<td>" . (file_exists($card['back_image_path']) ? "✅" : "❌") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test thumbnails directory
    echo "<h2>Thumbnails Directory Test</h2>";
    if (is_dir('thumbnails')) {
        $thumbnails = scandir('thumbnails');
        $thumbnailCount = count($thumbnails) - 2; // Subtract . and ..
        echo "<p>✅ Thumbnails directory exists with {$thumbnailCount} files.</p>";
    } else {
        echo "<p>❌ Thumbnails directory does not exist.</p>";
    }
    
    echo "<h2>Test Complete</h2>";
    echo "<p><a href='view_all_cards.php'>Go to View All Cards Page</a></p>";
    echo "<p><a href='card_creation_image.php'>Go to Card Creation Page</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>


