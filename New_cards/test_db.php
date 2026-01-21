<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=virtual_id_cards', 'root', '');
    echo "Database connection successful\n";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . implode(', ', $tables) . "\n";
    
    // Check if there are any cards
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM cards");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of cards in database: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        // Show recent cards
        $stmt = $pdo->query("SELECT unique_id, card_type, created_at FROM cards ORDER BY created_at DESC LIMIT 5");
        $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Recent cards:\n";
        foreach ($cards as $card) {
            echo "- ID: " . $card['unique_id'] . ", Type: " . $card['card_type'] . ", Created: " . $card['created_at'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>

