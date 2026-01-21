<?php
// Simple database setup script

$db_host = 'db5019002075.hosting-data.io';
$db_name = 'dbs14962592';
$db_user = 'dbu4026357';
$db_pass = '77143Ray!@12345#123$%^7!088989';

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup Script</h2>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    echo "<p>✅ Database '$db_name' created/verified successfully!</p>";
    
    // Select the database
    $pdo->exec("USE `$db_name`");
    
    // Drop existing id_cards table if it exists (to recreate with new structure)
    try {
        $pdo->exec("DROP TABLE IF EXISTS id_cards");
        echo "<p>⚠️ Dropped existing id_cards table to recreate with new structure</p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ No existing id_cards table to drop</p>";
    }
    
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
    echo "<p>✅ Admins table created successfully!</p>";
    
    // Create id_cards table with ALL required columns
    $pdo->exec("
        CREATE TABLE id_cards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            card_type ENUM('child_id', 'service_dog', 'emergency_id', 'custom') NOT NULL,
            card_number VARCHAR(20) UNIQUE NOT NULL,
            qr_code VARCHAR(255) NOT NULL,
            qr_random_number VARCHAR(10) UNIQUE NOT NULL,
            card_color ENUM('red', 'blue') DEFAULT 'red',
            
            -- Personal Information
            full_name VARCHAR(100) NOT NULL,
            photo VARCHAR(255),
            date_of_birth DATE,
            address TEXT,
            phone VARCHAR(20),
            email VARCHAR(100),
            
            -- Child ID specific
            parent_guardian VARCHAR(100),
            parent_phone VARCHAR(20),
            height VARCHAR(10),
            weight VARCHAR(10),
            eye_color VARCHAR(20),
            hair_color VARCHAR(20),
            blood_type VARCHAR(5),
            allergies TEXT,
            medical_conditions TEXT,
            
            -- Service Dog specific
            animal_name VARCHAR(50),
            handler_name VARCHAR(100),
            registry_number VARCHAR(50),
            service_type VARCHAR(100),
            
            -- Emergency contacts
            emergency_contact_1_name VARCHAR(100),
            emergency_contact_1_phone VARCHAR(20),
            emergency_contact_2_name VARCHAR(100),
            emergency_contact_2_phone VARCHAR(20),
            
            -- Additional fields
            notes TEXT,
            status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
            expiry_date DATE,
            
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (created_by) REFERENCES admins(id)
        )
    ");
    echo "<p>✅ ID cards table created successfully with all required columns!</p>";
    
    // Check if default admin exists, if not create one
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $default_password, 'admin@shieldid.us']);
        echo "<p>✅ Default admin user created (username: admin, password: admin123)</p>";
    } else {
        echo "<p>ℹ️ Admin user already exists</p>";
    }
    
    // Verify table structure
    $stmt = $pdo->query("DESCRIBE id_cards");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>✅ Database Setup Complete!</h3>";
    echo "<p><strong>Columns in id_cards table:</strong></p>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this setup_database.php file for security</li>";
    echo "<li>Try creating a new ID card - it should work now!</li>";
    echo "<li>You can select Red or Blue color for cards</li>";
    echo "<li>Each card will get a unique 10-digit QR token</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 