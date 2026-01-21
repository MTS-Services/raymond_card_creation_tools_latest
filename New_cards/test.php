<?php
// Test file to verify PHP setup and image processing capabilities
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Editor - System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-item { margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
    </style>
</head>
<body>
    <h1>Card Editor System Test</h1>
    
    <?php
    $tests = [];
    
    // Test 1: PHP Version
    $tests[] = [
        'name' => 'PHP Version',
        'result' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, '7.0.0', '>=') ? 'success' : 'error'
    ];
    
    // Test 2: GD Extension
    $tests[] = [
        'name' => 'GD Extension',
        'result' => extension_loaded('gd') ? 'Available' : 'Not Available',
        'status' => extension_loaded('gd') ? 'success' : 'error'
    ];
    
    // Test 3: File Permissions
    $tests[] = [
        'name' => 'Directory Write Permissions',
        'result' => is_writable('.') ? 'Writable' : 'Not Writable',
        'status' => is_writable('.') ? 'success' : 'error'
    ];
    
    // Test 4: Image Files
    $imageFiles = glob('*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $tests[] = [
        'name' => 'Image Files Found',
        'result' => count($imageFiles) . ' images found',
        'status' => count($imageFiles) > 0 ? 'success' : 'warning'
    ];
    
    // Test 5: Thumbnails Directory
    $thumbDir = 'thumbnails/';
    if (!is_dir($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }
    $tests[] = [
        'name' => 'Thumbnails Directory',
        'result' => is_dir($thumbDir) ? 'Created' : 'Failed to create',
        'status' => is_dir($thumbDir) ? 'success' : 'error'
    ];
    
    // Test 6: Edited Cards Directory
    $editedDir = 'edited_cards/';
    if (!is_dir($editedDir)) {
        mkdir($editedDir, 0755, true);
    }
    $tests[] = [
        'name' => 'Edited Cards Directory',
        'result' => is_dir($editedDir) ? 'Created' : 'Failed to create',
        'status' => is_dir($editedDir) ? 'success' : 'error'
    ];
    
    // Test 7: JSON Support
    $tests[] = [
        'name' => 'JSON Support',
        'result' => function_exists('json_encode') ? 'Available' : 'Not Available',
        'status' => function_exists('json_encode') ? 'success' : 'error'
    ];
    
    // Test 8: File Upload Support
    $tests[] = [
        'name' => 'File Upload Support',
        'result' => ini_get('file_uploads') ? 'Enabled' : 'Disabled',
        'status' => ini_get('file_uploads') ? 'success' : 'warning'
    ];
    
    // Test 9: Upload Max Size
    $uploadMaxSize = ini_get('upload_max_filesize');
    $tests[] = [
        'name' => 'Upload Max File Size',
        'result' => $uploadMaxSize,
        'status' => 'info'
    ];
    
    // Test 10: Post Max Size
    $postMaxSize = ini_get('post_max_size');
    $tests[] = [
        'name' => 'Post Max Size',
        'result' => $postMaxSize,
        'status' => 'info'
    ];
    
    // Display test results
    foreach ($tests as $test) {
        $statusClass = $test['status'];
        echo "<div class='test-item {$statusClass}'>";
        echo "<strong>{$test['name']}:</strong> {$test['result']}";
        echo "</div>";
    }
    
    // Display image files if found
    if (count($imageFiles) > 0) {
        echo "<h2>Available Images:</h2>";
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;'>";
        foreach ($imageFiles as $image) {
            $size = filesize($image);
            $sizeFormatted = $size > 1024 * 1024 ? round($size / (1024 * 1024), 2) . ' MB' : round($size / 1024, 2) . ' KB';
            echo "<div style='border: 1px solid #ddd; padding: 10px; border-radius: 5px;'>";
            echo "<strong>{$image}</strong><br>";
            echo "Size: {$sizeFormatted}<br>";
            echo "<img src='{$image}' style='max-width: 100%; height: auto; max-height: 100px; object-fit: cover;'>";
            echo "</div>";
        }
        echo "</div>";
    }
    ?>
    
    <h2>Next Steps:</h2>
    <ol>
        <li>If all tests show green (success), your system is ready!</li>
        <li>Open <a href="index.html">index.html</a> to start using the Card Editor</li>
        <li>If any tests show red (error), fix the issues before proceeding</li>
        <li>For yellow (warning) items, the tool may work but with limited functionality</li>
    </ol>
    
    <p><strong>Note:</strong> This test file can be deleted after confirming everything works correctly.</p>
</body>
</html>


