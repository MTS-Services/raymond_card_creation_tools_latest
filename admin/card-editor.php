<?php
require_once '../Middleware/Authentication.php';
require_once '../config/database.php';
new Authentication;

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'upload_image') {
        $upload_dir = '../uploads/card_designs/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if (isset($_FILES['card_image']) && $_FILES['card_image']['error'] === 0) {
            $file_info = pathinfo($_FILES['card_image']['name']);
            $file_extension = strtolower($file_info['extension']);
            
            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_filename = 'card_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['card_image']['tmp_name'], $upload_path)) {
                    $success_message = "Image uploaded successfully!";
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Invalid file type. Please upload JPG, PNG, or GIF files only.";
            }
        }
    }
}

// Get available images from New Cards folder
$new_cards_dir = '../New_cards/';
$available_images = [];

if (is_dir($new_cards_dir)) {
    $files = scandir($new_cards_dir);
    foreach ($files as $file) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $available_images[] = $file;
        }
    }
}

// Get uploaded card designs
$designs_dir = '../uploads/card_designs/';
$uploaded_designs = [];

if (is_dir($designs_dir)) {
    $files = scandir($designs_dir);
    foreach ($files as $file) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $uploaded_designs[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Editor - ShieldID Admin</title>
    <link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .editor-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .canvas-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .toolbar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .image-gallery {
            max-height: 400px;
            overflow-y: auto;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 15px;
            background: #f8f9fa;
        }
        .image-item {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .image-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .image-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .image-item.selected {
            border: 3px solid #007bff;
            box-shadow: 0 0 20px rgba(0,123,255,0.3);
        }
        .text-controls {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .color-picker {
            width: 50px;
            height: 40px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-tool {
            margin: 5px;
            border-radius: 25px;
            padding: 8px 16px;
        }
        .canvas-wrapper {
            position: relative;
            display: inline-block;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
        }
        .canvas-overlay {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        .text-element {
            position: absolute;
            cursor: move;
            padding: 5px;
            border: 1px dashed transparent;
            border-radius: 3px;
            min-width: 50px;
            min-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            user-select: none;
        }
        .text-element:hover {
            border-color: #007bff;
            background: rgba(0,123,255,0.1);
        }
        .text-element.selected {
            border-color: #007bff;
            background: rgba(0,123,255,0.2);
        }
        .text-element .resize-handle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #007bff;
            border-radius: 50%;
            cursor: se-resize;
            bottom: -4px;
            right: -4px;
        }
    </style>
</head>
<body style="background: #f0f2f5;">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="container-fluid" style="margin-left: 250px; padding: 20px;">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-palette text-primary"></i> Card Editor & Designer</h2>
                    <div>
                        <button class="btn btn-success me-2" onclick="saveDesign()">
                            <i class="fas fa-save"></i> Save Design
                        </button>
                        <button class="btn btn-primary" onclick="downloadDesign()">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Image Selection Panel -->
                    <div class="col-md-3">
                        <div class="editor-container">
                            <h5><i class="fas fa-images"></i> Available Images</h5>
                            
                            <!-- Upload New Image -->
                            <div class="mb-3">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="upload_image">
                                    <div class="mb-2">
                                        <input type="file" name="card_image" class="form-control form-control-sm" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-upload"></i> Upload Image
                                    </button>
                                </form>
                            </div>

                            <!-- New Cards Folder -->
                            <h6 class="text-primary">New Cards Folder</h6>
                            <div class="image-gallery">
                                <?php if (empty($available_images)): ?>
                                    <p class="text-muted text-center">No images found</p>
                                <?php else: ?>
                                    <?php foreach ($available_images as $image): ?>
                                        <div class="image-item" onclick="selectImage('<?= $image ?>', 'new_cards')">
                                            <img src="../New_cards/<?= $image ?>" alt="<?= $image ?>" title="<?= $image ?>">
                                            <small class="d-block text-center p-2"><?= $image ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Uploaded Designs -->
                            <h6 class="text-success mt-3">Uploaded Designs</h6>
                            <div class="image-gallery">
                                <?php if (empty($uploaded_designs)): ?>
                                    <p class="text-muted text-center">No designs uploaded yet</p>
                                <?php else: ?>
                                    <?php foreach ($uploaded_designs as $design): ?>
                                        <div class="image-item" onclick="selectImage('<?= $design ?>', 'designs')">
                                            <img src="../uploads/card_designs/<?= $design ?>" alt="<?= $design ?>" title="<?= $design ?>">
                                            <small class="d-block text-center p-2"><?= $design ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Main Editor -->
                    <div class="col-md-6">
                        <div class="editor-container">
                            <h5><i class="fas fa-edit"></i> Card Editor</h5>
                            
                            <div class="canvas-container">
                                <div class="canvas-wrapper">
                                    <canvas id="cardCanvas" width="600" height="400" style="border: 1px solid #ccc;"></canvas>
                                    <div id="canvasOverlay" class="canvas-overlay"></div>
                                </div>
                            </div>

                            <!-- Toolbar -->
                            <div class="toolbar">
                                <h6>Tools</h6>
                                <div class="d-flex flex-wrap">
                                    <button class="btn btn-tool btn-outline-primary" onclick="addText()">
                                        <i class="fas fa-font"></i> Add Text
                                    </button>
                                    <button class="btn btn-tool btn-outline-secondary" onclick="addRectangle()">
                                        <i class="fas fa-square"></i> Rectangle
                                    </button>
                                    <button class="btn btn-tool btn-outline-info" onclick="addCircle()">
                                        <i class="fas fa-circle"></i> Circle
                                    </button>
                                    <button class="btn btn-tool btn-outline-warning" onclick="clearCanvas()">
                                        <i class="fas fa-trash"></i> Clear
                                    </button>
                                    <button class="btn btn-tool btn-outline-success" onclick="undo()">
                                        <i class="fas fa-undo"></i> Undo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text & Design Controls -->
                    <div class="col-md-3">
                        <div class="text-controls">
                            <h5><i class="fas fa-cog"></i> Text Properties</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Text Content</label>
                                <input type="text" id="textContent" class="form-control" placeholder="Enter text...">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Font Size</label>
                                <input type="range" id="fontSize" class="form-range" min="8" max="72" value="16">
                                <small class="text-muted">Size: <span id="fontSizeValue">16</span>px</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Font Family</label>
                                <select id="fontFamily" class="form-select">
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Impact">Impact</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Text Color</label>
                                <input type="color" id="textColor" class="color-picker" value="#000000">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Background Color</label>
                                <input type="color" id="bgColor" class="color-picker" value="#ffffff">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Bold</label>
                                <input type="checkbox" id="boldText" class="form-check-input">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Italic</label>
                                <input type="checkbox" id="italicText" class="form-check-input">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Underline</label>
                                <input type="checkbox" id="underlineText" class="form-check-input">
                            </div>
                        </div>

                        <div class="text-controls">
                            <h5><i class="fas fa-layer-group"></i> Layer Management</h5>
                            <div id="layerList">
                                <p class="text-muted">No elements added yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML2Canvas for export -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let canvas, ctx;
        let currentImage = null;
        let textElements = [];
        let selectedElement = null;
        let isDragging = false;
        let dragOffset = { x: 0, y: 0 };
        let undoStack = [];
        let redoStack = [];

        // Initialize canvas
        document.addEventListener('DOMContentLoaded', function() {
            canvas = document.getElementById('cardCanvas');
            ctx = canvas.getContext('2d');
            
            // Set default canvas background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Add event listeners
            canvas.addEventListener('mousedown', startDrag);
            canvas.addEventListener('mousemove', drag);
            canvas.addEventListener('mouseup', endDrag);
            
            // Update font size display
            document.getElementById('fontSize').addEventListener('input', function() {
                document.getElementById('fontSizeValue').textContent = this.value;
            });
        });

        // Select image from gallery
        function selectImage(imageName, folder) {
            const image = new Image();
            image.onload = function() {
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Calculate scaling to fit canvas
                const scale = Math.min(canvas.width / image.width, canvas.height / image.height);
                const scaledWidth = image.width * scale;
                const scaledHeight = image.height * scale;
                const x = (canvas.width - scaledWidth) / 2;
                const y = (canvas.height - scaledHeight) / 2;
                
                // Draw image
                ctx.drawImage(image, x, y, scaledWidth, scaledHeight);
                currentImage = { name: imageName, folder: folder, scale: scale, offset: { x, y } };
                
                // Clear text elements
                textElements = [];
                updateLayerList();
                renderTextElements();
                
                // Save state for undo
                saveState();
            };
            
            if (folder === 'new_cards') {
                image.src = `../New_cards/${imageName}`;
            } else {
                image.src = `../uploads/card_designs/${imageName}`;
            }
        }

        // Add text element
        function addText() {
            const text = document.getElementById('textContent').value || 'Sample Text';
            const fontSize = parseInt(document.getElementById('fontSize').value);
            const fontFamily = document.getElementById('fontFamily').value;
            const color = document.getElementById('textColor').value;
            const bgColor = document.getElementById('bgColor').value;
            const bold = document.getElementById('boldText').checked;
            const italic = document.getElementById('italicText').checked;
            const underline = document.getElementById('underlineText').checked;
            
            const textElement = {
                id: Date.now(),
                type: 'text',
                text: text,
                x: 50,
                y: 50,
                fontSize: fontSize,
                fontFamily: fontFamily,
                color: color,
                bgColor: bgColor,
                bold: bold,
                italic: italic,
                underline: underline,
                width: 100,
                height: fontSize + 10
            };
            
            textElements.push(textElement);
            updateLayerList();
            renderTextElements();
            saveState();
        }

        // Add rectangle
        function addRectangle() {
            const rectElement = {
                id: Date.now(),
                type: 'rectangle',
                x: 100,
                y: 100,
                width: 100,
                height: 60,
                color: document.getElementById('textColor').value,
                bgColor: document.getElementById('bgColor').value
            };
            
            textElements.push(rectElement);
            updateLayerList();
            renderTextElements();
            saveState();
        }

        // Add circle
        function addCircle() {
            const circleElement = {
                id: Date.now(),
                type: 'circle',
                x: 150,
                y: 150,
                radius: 30,
                color: document.getElementById('textColor').value,
                bgColor: document.getElementById('bgColor').value
            };
            
            textElements.push(circleElement);
            updateLayerList();
            renderTextElements();
            saveState();
        }

        // Render all text elements
        function renderTextElements() {
            // Clear overlay
            const overlay = document.getElementById('canvasOverlay');
            overlay.innerHTML = '';
            
            textElements.forEach(element => {
                if (element.type === 'text') {
                    const textDiv = document.createElement('div');
                    textDiv.className = 'text-element';
                    textDiv.id = `element-${element.id}`;
                    textDiv.style.left = element.x + 'px';
                    textDiv.style.top = element.y + 'px';
                    textDiv.style.fontSize = element.fontSize + 'px';
                    textDiv.style.fontFamily = element.fontFamily;
                    textDiv.style.color = element.color;
                    textDiv.style.backgroundColor = element.bgColor;
                    textDiv.style.fontWeight = element.bold ? 'bold' : 'normal';
                    textDiv.style.fontStyle = element.italic ? 'italic' : 'normal';
                    textDiv.style.textDecoration = element.underline ? 'underline' : 'none';
                    textDiv.style.width = element.width + 'px';
                    textDiv.style.height = element.height + 'px';
                    textDiv.innerHTML = element.text + '<div class="resize-handle"></div>';
                    
                    textDiv.addEventListener('click', () => selectElement(element.id));
                    textDiv.addEventListener('dblclick', () => editElement(element.id));
                    
                    overlay.appendChild(textDiv);
                }
            });
        }

        // Select element
        function selectElement(elementId) {
            // Remove previous selection
            document.querySelectorAll('.text-element').forEach(el => el.classList.remove('selected'));
            
            // Select new element
            const element = textElements.find(el => el.id === elementId);
            if (element) {
                selectedElement = element;
                document.getElementById(`element-${elementId}`).classList.add('selected');
                
                // Update controls
                if (element.type === 'text') {
                    document.getElementById('textContent').value = element.text;
                    document.getElementById('fontSize').value = element.fontSize;
                    document.getElementById('fontFamily').value = element.fontFamily;
                    document.getElementById('textColor').value = element.color;
                    document.getElementById('bgColor').value = element.bgColor;
                    document.getElementById('boldText').checked = element.bold;
                    document.getElementById('italicText').checked = element.italic;
                    document.getElementById('underlineText').checked = element.underline;
                    document.getElementById('fontSizeValue').textContent = element.fontSize;
                }
            }
        }

        // Edit element
        function editElement(elementId) {
            const element = textElements.find(el => el.id === elementId);
            if (element && element.type === 'text') {
                const newText = prompt('Enter new text:', element.text);
                if (newText !== null) {
                    element.text = newText;
                    renderTextElements();
                    saveState();
                }
            }
        }

        // Update layer list
        function updateLayerList() {
            const layerList = document.getElementById('layerList');
            if (textElements.length === 0) {
                layerList.innerHTML = '<p class="text-muted">No elements added yet</p>';
                return;
            }
            
            layerList.innerHTML = '';
            textElements.forEach((element, index) => {
                const layerItem = document.createElement('div');
                layerItem.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded';
                layerItem.innerHTML = `
                    <div>
                        <i class="fas fa-${element.type === 'text' ? 'font' : element.type === 'rectangle' ? 'square' : 'circle'} me-2"></i>
                        ${element.type === 'text' ? element.text : element.type}
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteElement(${element.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                layerList.appendChild(layerItem);
            });
        }

        // Delete element
        function deleteElement(elementId) {
            textElements = textElements.filter(el => el.id !== elementId);
            updateLayerList();
            renderTextElements();
            saveState();
        }

        // Drag and drop functionality
        function startDrag(e) {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const element = textElements.find(el => 
                x >= el.x && x <= el.x + el.width &&
                y >= el.y && y <= el.y + el.height
            );
            
            if (element) {
                selectedElement = element;
                isDragging = true;
                dragOffset.x = x - element.x;
                dragOffset.y = y - element.y;
                
                // Select element visually
                selectElement(element.id);
            }
        }

        function drag(e) {
            if (isDragging && selectedElement) {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                selectedElement.x = x - dragOffset.x;
                selectedElement.y = y - dragOffset.y;
                
                renderTextElements();
            }
        }

        function endDrag() {
            if (isDragging) {
                isDragging = false;
                saveState();
            }
        }

        // Save state for undo
        function saveState() {
            undoStack.push(JSON.stringify(textElements));
            if (undoStack.length > 10) undoStack.shift();
            redoStack = [];
        }

        // Undo
        function undo() {
            if (undoStack.length > 0) {
                redoStack.push(JSON.stringify(textElements));
                textElements = JSON.parse(undoStack.pop());
                updateLayerList();
                renderTextElements();
            }
        }

        // Clear canvas
        function clearCanvas() {
            if (confirm('Are you sure you want to clear all elements?')) {
                textElements = [];
                updateLayerList();
                renderTextElements();
                saveState();
            }
        }

        // Save design
        function saveDesign() {
            // This would save the design to database
            alert('Design saved successfully!');
        }

        // Download design
        function downloadDesign() {
            const canvasWrapper = document.querySelector('.canvas-wrapper');
            
            html2canvas(canvasWrapper, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'custom_card_design.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        }

        // Auto-save text properties when changed
        document.getElementById('textContent').addEventListener('input', updateSelectedElement);
        document.getElementById('fontSize').addEventListener('input', updateSelectedElement);
        document.getElementById('fontFamily').addEventListener('change', updateSelectedElement);
        document.getElementById('textColor').addEventListener('change', updateSelectedElement);
        document.getElementById('bgColor').addEventListener('change', updateSelectedElement);
        document.getElementById('boldText').addEventListener('change', updateSelectedElement);
        document.getElementById('italicText').addEventListener('change', updateSelectedElement);
        document.getElementById('underlineText').addEventListener('change', updateSelectedElement);

        function updateSelectedElement() {
            if (selectedElement && selectedElement.type === 'text') {
                selectedElement.text = document.getElementById('textContent').value;
                selectedElement.fontSize = parseInt(document.getElementById('fontSize').value);
                selectedElement.fontFamily = document.getElementById('fontFamily').value;
                selectedElement.color = document.getElementById('textColor').value;
                selectedElement.bgColor = document.getElementById('bgColor').value;
                selectedElement.bold = document.getElementById('boldText').checked;
                selectedElement.italic = document.getElementById('italicText').checked;
                selectedElement.underline = document.getElementById('underlineText').checked;
                
                renderTextElements();
            }
        }
    </script>
</body>
</html>




