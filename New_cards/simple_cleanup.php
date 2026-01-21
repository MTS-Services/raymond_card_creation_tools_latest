<?php
// Simple script to clean up existing card IDs
echo "<h1>Card ID Cleanup Script</h1>";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'virtual_id_cards';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connected to database successfully</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Function to extract numeric ID
function extractNumericId($oldId) {
    if (preg_match('/^CARD_(\d+)_/', $oldId, $matches)) {
        return $matches[1];
    }
    return $oldId;
}

try {
    // Get all cards with old format
    $stmt = $pdo->query("SELECT id FROM cards WHERE id LIKE 'CARD_%'");
    $oldCards = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Found " . count($oldCards) . " cards with old ID format</h2>";
    
    if (count($oldCards) == 0) {
        echo "<p style='color: green;'>✅ All cards already have numeric IDs!</p>";
        exit;
    }
    
    echo "<h3>Cards to be updated:</h3>";
    echo "<ul>";
    foreach ($oldCards as $oldId) {
        $newId = extractNumericId($oldId);
        echo "<li>$oldId → $newId</li>";
    }
    echo "</ul>";
    
    // Check if user wants to proceed
    if (!isset($_POST['confirm'])) {
        echo "<form method='post'>";
        echo "<p><strong>Click the button below to update all IDs:</strong></p>";
        echo "<input type='submit' name='confirm' value='Update All IDs' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
        echo "</form>";
    } else {
        echo "<h2>Updating IDs...</h2>";
        
        $updatedCount = 0;
        $errorCount = 0;
        
        foreach ($oldCards as $oldId) {
            $newId = extractNumericId($oldId);
            
            if ($newId !== $oldId) {
                try {
                    // Check if new ID already exists
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE id = ?");
                    $checkStmt->execute([$newId]);
                    $exists = $checkStmt->fetchColumn();
                    
                    if ($exists > 0) {
                        echo "<p style='color: orange;'>⚠️ ID $newId already exists, skipping $oldId</p>";
                        $errorCount++;
                        continue;
                    }
                    
                    // Update the record
                    $updateStmt = $pdo->prepare("UPDATE cards SET id = ? WHERE id = ?");
                    $updateStmt->execute([$newId, $oldId]);
                    
                    echo "<p style='color: green;'>✅ Updated: $oldId → $newId</p>";
                    $updatedCount++;
                    
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>❌ Error updating $oldId: " . $e->getMessage() . "</p>";
                    $errorCount++;
                }
            }
        }
        
        echo "<h3>Summary</h3>";
        echo "<p><strong>Successfully updated:</strong> $updatedCount records</p>";
        echo "<p><strong>Errors/Skipped:</strong> $errorCount records</p>";
        
        if ($updatedCount > 0) {
            echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 Database cleanup completed successfully!</p>";
            echo "<p><a href='view_all_cards.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Updated Cards</a></p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Show current database info
echo "<hr>";
echo "<h3>Database Information</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cards");
    $total = $stmt->fetchColumn();
    echo "<p>Total cards in database: $total</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as old_format FROM cards WHERE id LIKE 'CARD_%'");
    $oldFormat = $stmt->fetchColumn();
    echo "<p>Cards with old format: $oldFormat</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as new_format FROM cards WHERE id NOT LIKE 'CARD_%'");
    $newFormat = $stmt->fetchColumn();
    echo "<p>Cards with new format: $newFormat</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Could not retrieve database info: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
h1, h2, h3 { color: #333; }
ul { background: white; padding: 15px; border-radius: 5px; }
li { margin: 5px 0; }
</style>


