<?php
/**
 * Test Card Editor Functionality
 * This file helps verify that the Card Editor is working correctly
 */

echo "<h1>ShieldID Card Editor Test</h1>";

// Check if we can access the New Cards folder
$new_cards_dir = 'New_cards/';
echo "<h2>1. New Cards Folder Check</h2>";
if (is_dir($new_cards_dir)) {
    echo "✅ New Cards folder exists<br>";
    $files = scandir($new_cards_dir);
    $image_count = 0;
    foreach ($files as $file) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $image_count++;
        }
    }
    echo "✅ Found {$image_count} image files<br>";
} else {
    echo "❌ New Cards folder not found<br>";
}

// Check if we can access the uploads directory
$uploads_dir = 'uploads/card_designs/';
echo "<h2>2. Uploads Directory Check</h2>";
if (is_dir($uploads_dir)) {
    echo "✅ Uploads directory exists<br>";
    if (is_writable($uploads_dir)) {
        echo "✅ Uploads directory is writable<br>";
    } else {
        echo "❌ Uploads directory is not writable<br>";
    }
} else {
    echo "❌ Uploads directory not found<br>";
    // Try to create it
    if (mkdir($uploads_dir, 0755, true)) {
        echo "✅ Created uploads directory<br>";
    } else {
        echo "❌ Failed to create uploads directory<br>";
    }
}

// Check if card-editor.php exists
echo "<h2>3. Card Editor File Check</h2>";
if (file_exists('admin/card-editor.php')) {
    echo "✅ Card Editor file exists<br>";
} else {
    echo "❌ Card Editor file not found<br>";
}

// Check if sidebar has been updated
echo "<h2>4. Sidebar Integration Check</h2>";
$sidebar_content = file_get_contents('admin/includes/sidebar.php');
if (strpos($sidebar_content, 'card-editor.php') !== false) {
    echo "✅ Card Editor link found in sidebar<br>";
} else {
    echo "❌ Card Editor link not found in sidebar<br>";
}

// Check database connection
echo "<h2>5. Database Connection Check</h2>";
try {
    require_once 'config/database.php';
    echo "✅ Database configuration loaded<br>";
    
    // Test connection
    $test_stmt = $pdo->query("SELECT 1");
    if ($test_stmt) {
        echo "✅ Database connection successful<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Check required libraries
echo "<h2>6. Required Libraries Check</h2>";
$libraries = [
    'Bootstrap 5.3.0' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'Font Awesome 6.0.0' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'HTML2Canvas 1.4.1' => 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js'
];

foreach ($libraries as $name => $url) {
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "✅ {$name} is accessible<br>";
    } else {
        echo "❌ {$name} is not accessible<br>";
    }
}

echo "<h2>7. Quick Start Instructions</h2>";
echo "<ol>";
echo "<li>Log into the admin panel</li>";
echo "<li>Navigate to 'Card Editor' in the sidebar</li>";
echo "<li>Select an image from the New Cards folder</li>";
echo "<li>Add text elements using the Add Text button</li>";
echo "<li>Customize text properties in the right panel</li>";
echo "<li>Drag elements to position them on the canvas</li>";
echo "<li>Use the Download button to export your design</li>";
echo "</ol>";

echo "<h2>8. Troubleshooting</h2>";
echo "<ul>";
echo "<li><strong>Images not loading:</strong> Check file permissions and browser console</li>";
echo "<li><strong>Text elements not appearing:</strong> Ensure JavaScript is enabled</li>";
echo "<li><strong>Export not working:</strong> Check browser compatibility and HTML2Canvas loading</li>";
echo "<li><strong>Performance issues:</strong> Use smaller images and limit text elements</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Test completed!</strong> If you see any ❌ marks above, those issues need to be resolved before using the Card Editor.</p>";
echo "<p><a href='admin/card-editor.php'>Go to Card Editor</a> (requires admin login)</p>";
?>




