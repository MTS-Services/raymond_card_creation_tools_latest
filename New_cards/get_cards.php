<?php
// Increase memory limit for large images
ini_set('memory_limit', '256M');

// Ensure clean output - no whitespace or HTML before JSON
ob_start();
header('Content-Type: application/json');

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Function to create thumbnail
function createThumbnail($imagePath, $thumbnailPath, $maxWidth = 60, $maxHeight = 40) {
    if (!file_exists($imagePath)) {
        return false;
    }

    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) {
        return false;
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $type = $imageInfo[2];

    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);

    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($imagePath);
            break;
        default:
            return false;
    }

    if (!$source) {
        return false;
    }

    // Create thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize image
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save thumbnail
    $thumbnailDir = 'thumbnails/';
    if (!is_dir($thumbnailDir)) {
        mkdir($thumbnailDir, 0755, true);
    }

    $thumbnailPath = $thumbnailDir . basename($imagePath);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $thumbnailPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $thumbnailPath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $thumbnailPath);
            break;
    }

    // Clean up
    imagedestroy($source);
    imagedestroy($thumbnail);

    return $thumbnailPath;
}

try {
    $cards = [];
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Scan current directory for image files
    $files = scandir(__DIR__);
    
    // Debug: Log what we found (commented out to prevent JSON corruption)
    // error_log("Debug: Found " . count($files) . " files in directory");
    
    foreach ($files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        // Debug: Log each file (commented out to prevent JSON corruption)
        // error_log("Debug: Checking file: $file (extension: $extension)");
        
        if (in_array($extension, $imageExtensions)) {
            $filePath = $file;
            $fileSize = filesize($file);
            $thumbnailPath = 'thumbnails/' . $file;
            
            // Skip very large files that might cause memory issues
            if ($fileSize > 50 * 1024 * 1024) { // Skip files larger than 50MB
                continue;
            }
            
            // error_log("Debug: Found image file: $file (size: $fileSize)");
            
            // Create thumbnail if it doesn't exist
            if (!file_exists($thumbnailPath)) {
                try {
                    $thumbnailPath = createThumbnail($filePath, $thumbnailPath);
                    // error_log("Debug: Created thumbnail: $thumbnailPath");
                } catch (Exception $e) {
                    // Skip files that can't be processed
                    continue;
                }
            }
            
            if ($thumbnailPath) {
                $cards[] = [
                    'name' => $file,
                    'path' => $filePath,
                    'thumbnail' => $thumbnailPath,
                    'size' => formatFileSize($fileSize),
                    'extension' => $extension
                ];
            }
        }
    }
    
    // Sort cards by name
    usort($cards, function($a, $b) {
        return strnatcmp($a['name'], $b['name']);
    });
    
    $output = json_encode([
        'success' => true,
        'cards' => $cards,
        'total' => count($cards)
    ]);
    
    // Clean any output buffer and send clean JSON
    ob_clean();
    echo $output;

} catch (Exception $e) {
    $output = json_encode([
        'success' => false,
        'message' => 'Error scanning directory: ' . $e->getMessage()
    ]);
    
    // Clean any output buffer and send clean JSON
    ob_clean();
    echo $output;
}
?>
