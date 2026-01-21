<?php
require_once 'config/database.php';

echo "<h2>Database Update Script</h2>";

try {
    // Initialize database to ensure tables exist
    initializeDatabase();
    
    echo "<p>✅ Database initialization completed successfully!</p>";
    
    // Check if columns exist
    $stmt = $pdo->query("DESCRIBE id_cards");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Current columns in id_cards table:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Check if we need to add missing columns
    if (!in_array('qr_random_number', $columns)) {
        echo "<p>⚠️ Adding qr_random_number column...</p>";
        $pdo->exec("ALTER TABLE id_cards ADD COLUMN qr_random_number VARCHAR(10) UNIQUE NOT NULL DEFAULT '0000000000'");
        echo "<p>✅ qr_random_number column added successfully!</p>";
    } else {
        echo "<p>✅ qr_random_number column already exists</p>";
    }
    
    if (!in_array('card_color', $columns)) {
        echo "<p>⚠️ Adding card_color column...</p>";
        $pdo->exec("ALTER TABLE id_cards ADD COLUMN card_color ENUM('red', 'blue') DEFAULT 'red'");
        echo "<p>✅ card_color column added successfully!</p>";
    } else {
        echo "<p>✅ card_color column already exists</p>";
    }
    
    // Update existing records with valid qr_random_number
    $stmt = $pdo->query("SELECT COUNT(*) FROM id_cards WHERE qr_random_number = '0000000000' OR qr_random_number IS NULL");
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo "<p>⚠️ Updating $count existing records with valid qr_random_number...</p>";
        $pdo->exec("UPDATE id_cards SET qr_random_number = CONCAT('0000000000', id) WHERE qr_random_number = '0000000000' OR qr_random_number IS NULL");
        echo "<p>✅ Existing records updated successfully!</p>";
    } else {
        echo "<p>✅ All existing records already have valid qr_random_number</p>";
    }
    
    // Final verification
    $stmt = $pdo->query("SELECT COUNT(*) FROM id_cards");
    $total_cards = $stmt->fetchColumn();
    
    echo "<h3>✅ Database Update Complete!</h3>";
    echo "<p>Total ID cards in database: $total_cards</p>";
    echo "<p>You can now create new ID cards with color selection and QR codes.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 