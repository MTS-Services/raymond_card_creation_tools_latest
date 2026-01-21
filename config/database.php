<?php
// Database configuration


$db_host = 'db5019002075.hosting-data.io';
$db_name = 'dbs14962592';
$db_user = 'dbu4026357';
$db_pass = '77143Ray!@12345#123$%^7!088989';



try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create database and tables if they don't exist
function initializeDatabase() {
    global $pdo, $db_name;
    
    // Create admins table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create cards table (New Cards database structure)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            card_type VARCHAR(50) NOT NULL,
            unique_id VARCHAR(100) UNIQUE NOT NULL,
            qr_code_url VARCHAR(500) NOT NULL,
            front_image_path VARCHAR(500) NOT NULL,
            back_image_path VARCHAR(500) NOT NULL,
            card_data JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Create card_fields table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS card_fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            card_id INT NOT NULL,
            field_name VARCHAR(100) NOT NULL,
            field_value TEXT,
            FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
        )
    ");
    
    // Create indexes for better performance
    try {
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_cards_unique_id ON cards(unique_id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_cards_qr_url ON cards(qr_code_url)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_card_fields_card_id ON card_fields(card_id)");
    } catch (Exception $e) {
        // Indexes might already exist, ignore error
    }
    
    // Check if cards table exists and has data
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM cards");
        $cardCount = $stmt->fetchColumn();
        // Debug info removed - no longer displaying card count
    } catch (Exception $e) {
        // Cards table not found or empty - silent handling
    }
    
    // Check if default admin exists, if not create one
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $default_password, 'admin@shieldid.us']);
    }
}
?>