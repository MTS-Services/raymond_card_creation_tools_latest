<?php
// Script to clean up the unique_id column (not the id column)
echo "<h1>Card Unique ID Cleanup Script</h1>";

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
    // Get all cards with old unique_id format
    $stmt = $pdo->query("SELECT id, unique_id, card_type FROM cards WHERE unique_id LIKE 'CARD_%'");
    $oldCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Found " . count($oldCards) . " cards with old unique_id format</h2>";
    
    if (count($oldCards) == 0) {
        echo "<p style='color: green;'>✅ All cards already have numeric unique_ids!</p>";
        exit;
    }
    
    echo "<h3>Cards to be updated:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>DB ID</th><th>Card Type</th><th>Current Unique ID</th><th>New Unique ID</th></tr>";
    
    foreach ($oldCards as $card) {
        $newId = extractNumericId($card['unique_id']);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($card['id']) . "</td>";
        echo "<td>" . htmlspecialchars($card['card_type']) . "</td>";
        echo "<td>" . htmlspecialchars($card['unique_id']) . "</td>";
        echo "<td>" . htmlspecialchars($newId) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if user wants to proceed
    if (!isset($_POST['confirm'])) {
        echo "<br><form method='post'>";
        echo "<p><strong>Click the button below to update all unique_ids:</strong></p>";
        echo "<input type='submit' name='confirm' value='Update All Unique IDs' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
        echo "</form>";
    } else {
        echo "<h2>Updating Unique IDs...</h2>";
        
        $updatedCount = 0;
        $errorCount = 0;
        
        foreach ($oldCards as $card) {
            $oldUniqueId = $card['unique_id'];
            $newUniqueId = extractNumericId($oldUniqueId);
            
            if ($newUniqueId !== $oldUniqueId) {
                try {
                    // Check if new unique_id already exists
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE unique_id = ?");
                    $checkStmt->execute([$newUniqueId]);
                    $exists = $checkStmt->fetchColumn();
                    
                    if ($exists > 0) {
                        echo "<p style='color: orange;'>⚠️ Unique ID $newUniqueId already exists, skipping $oldUniqueId</p>";
                        $errorCount++;
                        continue;
                    }
                    
                    // Update the unique_id column
                    $updateStmt = $pdo->prepare("UPDATE cards SET unique_id = ? WHERE id = ?");
                    $updateStmt->execute([$newUniqueId, $card['id']]);
                    
                    echo "<p style='color: green;'>✅ Updated: $oldUniqueId → $newUniqueId</p>";
                    $updatedCount++;
                    
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>❌ Error updating $oldUniqueId: " . $e->getMessage() . "</p>";
                    $errorCount++;
                }
            }
        }
        
        echo "<h3>Summary</h3>";
        echo "<p><strong>Successfully updated:</strong> $updatedCount records</p>";
        echo "<p><strong>Errors/Skipped:</strong> $errorCount records</p>";
        
        if ($updatedCount > 0) {
            echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 Unique ID cleanup completed successfully!</p>";
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
    
    $stmt = $pdo->query("SELECT COUNT(*) as old_format FROM cards WHERE unique_id LIKE 'CARD_%'");
    $oldFormat = $stmt->fetchColumn();
    echo "<p>Cards with old unique_id format: $oldFormat</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as new_format FROM cards WHERE unique_id NOT LIKE 'CARD_%'");
    $newFormat = $stmt->fetchColumn();
    echo "<p>Cards with new unique_id format: $newFormat</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Could not retrieve database info: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
h1, h2, h3 { color: #333; }
table { background: white; border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>


