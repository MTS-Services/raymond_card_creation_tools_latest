<?php
require_once 'config/database.php';

echo "<h2>Database Structure Test</h2>";

try {
    // Check if database exists
    echo "<p>✅ Database connection successful</p>";
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE id_cards");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current id_cards table structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
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
    
    // Check if required columns exist
    $required_columns = ['qr_random_number', 'card_color'];
    $missing_columns = [];
    
    foreach ($required_columns as $col) {
        $exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $col) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $missing_columns[] = $col;
        }
    }
    
    if (empty($missing_columns)) {
        echo "<p style='color: green;'>✅ All required columns exist!</p>";
    } else {
        echo "<p style='color: red;'>❌ Missing columns: " . implode(', ', $missing_columns) . "</p>";
    }
    
    // Check sample data
    $stmt = $pdo->query("SELECT id, card_number, qr_random_number, card_color FROM id_cards LIMIT 5");
    $sample_data = $stmt->fetchAll();
    
    echo "<h3>Sample data:</h3>";
    if (empty($sample_data)) {
        echo "<p>No ID cards found in database</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Card Number</th><th>QR Random Number</th><th>Card Color</th></tr>";
        
        foreach ($sample_data as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['card_number']}</td>";
            echo "<td>{$row['qr_random_number']}</td>";
            echo "<td>{$row['card_color']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Next Steps:</h3>";
    if (!empty($missing_columns)) {
        echo "<p style='color: red;'>❌ You need to run the database setup script first!</p>";
        echo "<p><strong>Solution:</strong> Run <code>setup_database.php</code> to create the missing columns</p>";
    } else {
        echo "<p style='color: green;'>✅ Database structure is correct! QR codes should work now.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 