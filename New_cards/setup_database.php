<?php
/**
 * Database Setup Script
 * Run this file to automatically create the database and tables
 * Access via: http://localhost/virtual_id2/final_virtual_id/New%20Cards/setup_database.php
 */

// Database configuration
$host = 'localhost';
$username = 'root'; // Default for W
// AMP
$password = ''; // Default for WAMP
$dbname = 'virtual_id_cards';

echo "<h1>🗄️ Virtual ID Card Database Setup</h1>";
echo "<p>Setting up database and tables...</p>";

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Connected to MySQL server successfully</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "<p>✅ Database '$dbname' created successfully</p>";
    
    // Use the database
    $pdo->exec("USE `$dbname`");
    echo "<p>✅ Using database '$dbname'</p>";
    
    // Create cards table
    $createCardsTable = "
    CREATE TABLE IF NOT EXISTS `cards` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `card_type` VARCHAR(50) NOT NULL,
        `unique_id` VARCHAR(100) UNIQUE NOT NULL,
        `qr_code_url` VARCHAR(500) NOT NULL,
        `front_image_path` VARCHAR(500) NOT NULL,
        `back_image_path` VARCHAR(500) NOT NULL,
        `card_data` JSON NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createCardsTable);
    echo "<p>✅ Table 'cards' created successfully</p>";
    
    // Create card_fields table
    $createCardFieldsTable = "
    CREATE TABLE IF NOT EXISTS `card_fields` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `card_id` INT NOT NULL,
        `field_name` VARCHAR(100) NOT NULL,
        `field_value` TEXT,
        FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createCardFieldsTable);
    echo "<p>✅ Table 'card_fields' created successfully</p>";
    
    // Create indexes for better performance (with error handling for existing indexes)
    try {
        $pdo->exec("CREATE INDEX `idx_cards_unique_id` ON `cards`(`unique_id`)");
        echo "<p>✅ Index 'idx_cards_unique_id' created successfully</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "<p>ℹ️ Index 'idx_cards_unique_id' already exists</p>";
        } else {
            echo "<p>⚠️ Warning: Could not create index 'idx_cards_unique_id': " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("CREATE INDEX `idx_cards_qr_url` ON `cards`(`qr_code_url`)");
        echo "<p>✅ Index 'idx_cards_qr_url' created successfully</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "<p>ℹ️ Index 'idx_cards_qr_url' already exists</p>";
        } else {
            echo "<p>⚠️ Warning: Could not create index 'idx_cards_qr_url': " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $pdo->exec("CREATE INDEX `idx_card_fields_card_id` ON `card_fields`(`card_id`)");
        echo "<p>✅ Index 'idx_card_fields_card_id' created successfully</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "<p>ℹ️ Index 'idx_card_fields_card_id' already exists</p>";
        } else {
            echo "<p>⚠️ Warning: Could not create index 'idx_card_fields_card_id': " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p>✅ Database indexes setup completed</p>";
    
    // Create stored_cards directory if it doesn't exist
    $storedCardsDir = 'stored_cards';
    if (!file_exists($storedCardsDir)) {
        if (mkdir($storedCardsDir, 0777, true)) {
            echo "<p>✅ Directory '$storedCardsDir' created successfully</p>";
        } else {
            echo "<p>⚠️ Warning: Could not create directory '$storedCardsDir'. Please create it manually with write permissions.</p>";
        }
    } else {
        echo "<p>✅ Directory '$storedCardsDir' already exists</p>";
    }
    
    // Create thumbnails directory if it doesn't exist
    $thumbnailsDir = 'thumbnails';
    if (!file_exists($thumbnailsDir)) {
        if (mkdir($thumbnailsDir, 0777, true)) {
            echo "<p>✅ Directory '$thumbnailsDir' created successfully</p>";
        } else {
            echo "<p>⚠️ Warning: Could not create directory '$thumbnailsDir'. Please create it manually with write permissions.</p>";
        }
    } else {
        echo "<p>✅ Directory '$thumbnailsDir' already exists</p>";
    }
    
    // Test database connection with the new database
    $testPdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test query
    $stmt = $testPdo->query("SELECT COUNT(*) as count FROM cards");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>✅ Database connection test successful</p>";
    echo "<p>📊 Current cards in database: " . $result['count'] . "</p>";
    
    echo "<hr>";
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ What was created:</h3>";
    echo "<ul>";
    echo "<li><strong>Database:</strong> $dbname</li>";
    echo "<li><strong>Table:</strong> cards (main card information)</li>";
    echo "<li><strong>Table:</strong> card_fields (individual field data)</li>";
    echo "<li><strong>Directory:</strong> stored_cards/ (for card images)</li>";
    echo "<li><strong>Directory:</strong> thumbnails/ (for image thumbnails)</li>";
    echo "<li><strong>Indexes:</strong> For fast database queries</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🚀 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Your database is ready to use!</li>";
    echo "<li>Start creating cards with the <a href='card_creation_image.php'>Card Creation Tool</a></li>";
    echo "<li>Cards will be automatically saved to the database</li>";
    echo "<li>QR codes will link to the card display page</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>📋 Database Information:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> $host</li>";
    echo "<li><strong>Database:</strong> $dbname</li>";
    echo "<li><strong>Username:</strong> $username</li>";
    echo "<li><strong>Tables:</strong> cards, card_fields</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>🔧 Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running in WAMP</li>";
    echo "<li>Check if username 'root' and empty password are correct</li>";
    echo "<li>Verify MySQL service is started</li>";
    echo "<li>Try accessing phpMyAdmin to test connection</li>";
    echo "</ul>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Setup Error</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Generated on " . date('Y-m-d H:i:s') . "</small></p>";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}

h1 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 10px;
}

h2 {
    color: #34495e;
    margin-top: 30px;
}

h3 {
    color: #2c3e50;
    margin-top: 20px;
}

p {
    line-height: 1.6;
    margin: 10px 0;
}

ul, ol {
    margin: 10px 0;
    padding-left: 20px;
}

li {
    margin: 5px 0;
}

a {
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

hr {
    border: none;
    height: 2px;
    background: #ecf0f1;
    margin: 30px 0;
}
</style>
