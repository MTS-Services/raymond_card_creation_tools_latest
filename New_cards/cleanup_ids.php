<?php
// Script to clean up existing card IDs in the database
// This will remove CARD_ prefix and suffix, keeping only the numeric part

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "virtual_id_cards"; // Your actual database name

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully<br><br>";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to extract numeric ID from old format
function extractNumericId($oldId) {
    // Pattern: CARD_1234567890_abcdefgh
    // Extract the numeric part between CARD_ and _
    if (preg_match('/^CARD_(\d+)_/', $oldId, $matches)) {
        return $matches[1];
    }
    return $oldId; // Return original if pattern doesn't match
}

try {
    // First, let's see what we're working with
    echo "<h2>Current Data Analysis</h2>";
    $stmt = $pdo->query("SELECT id, card_type, created_at FROM cards ORDER BY created_at DESC LIMIT 10");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Current ID</th><th>Card Type</th><th>Created At</th><th>New ID (Preview)</th></tr>";
    
    foreach ($cards as $card) {
        $newId = extractNumericId($card['id']);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($card['id']) . "</td>";
        echo "<td>" . htmlspecialchars($card['card_type']) . "</td>";
        echo "<td>" . htmlspecialchars($card['created_at']) . "</td>";
        echo "<td>" . htmlspecialchars($newId) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Check if user wants to proceed
    if (!isset($_POST['confirm'])) {
        echo "<h2>Data Cleanup Confirmation</h2>";
        echo "<p><strong>Warning:</strong> This will update all existing card IDs in the database.</p>";
        echo "<p>The old format 'CARD_1234567890_abcdefgh' will become '1234567890'</p>";
        echo "<form method='post'>";
        echo "<input type='submit' name='confirm' value='Yes, Update All IDs' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
        echo "</form>";
        echo "<p><em>Make sure to backup your database before proceeding!</em></p>";
    } else {
        // Proceed with the update
        echo "<h2>Updating Database Records...</h2>";
        
        // Get all cards with old format
        $stmt = $pdo->query("SELECT id FROM cards WHERE id LIKE 'CARD_%'");
        $oldCards = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
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
                        echo "<p style='color: orange;'>Warning: ID $newId already exists, skipping $oldId</p>";
                        $errorCount++;
                        continue;
                    }
                    
                    // Update the record
                    $updateStmt = $pdo->prepare("UPDATE cards SET id = ? WHERE id = ?");
                    $updateStmt->execute([$newId, $oldId]);
                    
                    echo "<p style='color: green;'>Updated: $oldId → $newId</p>";
                    $updatedCount++;
                    
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>Error updating $oldId: " . $e->getMessage() . "</p>";
                    $errorCount++;
                }
            }
        }
        
        echo "<br><h3>Summary</h3>";
        echo "<p><strong>Successfully updated:</strong> $updatedCount records</p>";
        echo "<p><strong>Errors/Skipped:</strong> $errorCount records</p>";
        
        if ($updatedCount > 0) {
            echo "<p style='color: green; font-weight: bold;'>✅ Database cleanup completed successfully!</p>";
            echo "<p>You can now refresh your card list to see the updated IDs.</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Show current database structure for reference
echo "<br><h2>Database Table Structure</h2>";
try {
    $stmt = $pdo->query("DESCRIBE cards");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Could not retrieve table structure: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>
