<?php
require_once '../config/database.php';
require_once '../Middleware/Authentication.php';

new Authentication;

// Initialize database if needed
initializeDatabase();

$success = '';
$error = '';
$card_type = $_GET['type'] ?? 'child_id';

// Generate a one-time form token to prevent double submit
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['form_token'];

// Handle form submission
if ($_POST) {
    // Check token
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        $error = "Invalid form submission. Please refresh the page.";
    } else {
        try {
            // Prevent double submission by regenerating token
            unset($_SESSION['form_token']);

            // Generate unique card number
            do {
                $card_number = 'SID' . date('Y') . sprintf('%08d', mt_rand(1, 99999999));
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM id_cards WHERE card_number = ?");
                $stmt->execute([$card_number]);
            } while ($stmt->fetchColumn() > 0);

            // Generate unique 10-digit random number for QR code
            do {
                $qr_random_number = sprintf('%010d', mt_rand(1, 9999999999));
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM id_cards WHERE qr_random_number = ?");
                $stmt->execute([$qr_random_number]);
            } while ($stmt->fetchColumn() > 0);

            // Handle photo upload
            $photo_path = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $upload_dir = '../uploads/photos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo_filename = $card_number . '_' . time() . '.' . $file_extension;
                $photo_path = $upload_dir . $photo_filename;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                    $photo_path = 'uploads/photos/' . $photo_filename;
                } else {
                    $photo_path = null;
                }
            }

            // Generate QR code data (URL to view the card with token)
            $qr_data = "https://shieldid.us/view-card.php?token=" . $qr_random_number . "&id=" . $card_number;

            // Prepare data for insertion
            $card_data = [
                'card_type' => $_POST['card_type'],
                'card_number' => $card_number,
                'qr_code' => $qr_data,
                'qr_random_number' => $qr_random_number,
                'card_color' => $_POST['card_color'],
                'full_name' => $_POST['full_name'],
                'photo' => $photo_path,
                'date_of_birth' => $_POST['date_of_birth'] ?: null,
                'address' => $_POST['address'] ?: null,
                'phone' => $_POST['phone'] ?: null,
                'email' => $_POST['email'] ?: null,
                'parent_guardian' => $_POST['parent_guardian'] ?: null,
                'parent_phone' => $_POST['parent_phone'] ?: null,
                'height' => $_POST['height'] ?: null,
                'weight' => $_POST['weight'] ?: null,
                'eye_color' => $_POST['eye_color'] ?: null,
                'hair_color' => $_POST['hair_color'] ?: null,
                'blood_type' => $_POST['blood_type'] ?: null,
                'allergies' => $_POST['allergies'] ?: null,
                'medical_conditions' => $_POST['medical_conditions'] ?: null,
                'animal_name' => $_POST['animal_name'] ?: null,
                'handler_name' => $_POST['handler_name'] ?: null,
                'registry_number' => $_POST['registry_number'] ?: null,
                'service_type' => $_POST['service_type'] ?: null,
                'emergency_contact_1_name' => $_POST['emergency_contact_1_name'] ?: null,
                'emergency_contact_1_phone' => $_POST['emergency_contact_1_phone'] ?: null,
                'emergency_contact_2_name' => $_POST['emergency_contact_2_name'] ?: null,
                'emergency_contact_2_phone' => $_POST['emergency_contact_2_phone'] ?: null,
                'notes' => $_POST['notes'] ?: null,
                'expiry_date' => $_POST['expiry_date'] ?: null,
                'created_by' => $_SESSION['admin_id']
            ];

            // Insert into database
            $sql = "INSERT INTO id_cards (
                card_type, card_number, qr_code, qr_random_number, card_color, full_name, photo, date_of_birth, address, phone, email,
                parent_guardian, parent_phone, height, weight, eye_color, hair_color, blood_type,
                allergies, medical_conditions, animal_name, handler_name, registry_number, service_type,
                emergency_contact_1_name, emergency_contact_1_phone, emergency_contact_2_name, emergency_contact_2_phone,
                notes, expiry_date, created_by
            ) VALUES (
                :card_type, :card_number, :qr_code, :qr_random_number, :card_color, :full_name, :photo, :date_of_birth, :address, :phone, :email,
                :parent_guardian, :parent_phone, :height, :weight, :eye_color, :hair_color, :blood_type,
                :allergies, :medical_conditions, :animal_name, :handler_name, :registry_number, :service_type,
                :emergency_contact_1_name, :emergency_contact_1_phone, :emergency_contact_2_name, :emergency_contact_2_phone,
                :notes, :expiry_date, :created_by
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($card_data);

            $success = "ID Card created successfully! Card Number: " . $card_number . " | QR Token: " . $qr_random_number;

        } catch (Exception $e) {
            $error = "Error creating card: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New ID Card - ShieldID</title>
  	<link rel="icon" href="../favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
    <style>
        .form-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 15px 15px 0 0;
            margin: -1px -1px 20px -1px;
        }
        
        .photo-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .type-specific {
            display: none;
        }
        
        .type-specific.active {
            display: block;
        }
        
        .color-preview {
            width: 100px;
            height: 60px;
            border-radius: 8px;
            border: 3px solid #ddd;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .color-preview.selected {
            border-color: #007bff;
            transform: scale(1.05);
        }
        
        .color-option {
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .color-option:hover {
            background-color: #f8f9fa;
        }
        
        .color-option.selected {
            background-color: #e3f2fd;
            border: 2px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="pt-3 pb-2 mb-3">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Create New ID Card</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <a href="cards.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cards
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <!-- Card Type Selection -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Card Type</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="card_type" id="child_id" value="child_id" <?= $card_type == 'child_id' ? 'checked' : '' ?> onchange="toggleFields()">
                                    <label class="form-check-label" for="child_id">
                                        <i class="fas fa-child me-2 text-primary"></i>Child ID
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="card_type" id="service_dog" value="service_dog" <?= $card_type == 'service_dog' ? 'checked' : '' ?> onchange="toggleFields()">
                                    <label class="form-check-label" for="service_dog">
                                        <i class="fas fa-dog me-2 text-success"></i>Service Dog
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="card_type" id="emergency_id" value="emergency_id" <?= $card_type == 'emergency_id' ? 'checked' : '' ?> onchange="toggleFields()">
                                    <label class="form-check-label" for="emergency_id">
                                        <i class="fas fa-ambulance me-2 text-danger"></i>Emergency ID
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Color Selection -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Card Color</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="color-option" onclick="selectColor('red')">
                                    <input type="radio" name="card_color" value="red" id="color_red" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview" id="preview_red" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);"></div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Red Card</h6>
                                            <p class="text-muted mb-0">Classic red design for high visibility</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="color-option" onclick="selectColor('blue')">
                                    <input type="radio" name="card_color" value="blue" id="color_blue" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <div class="color-preview" id="preview_blue" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);"></div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">Blue Card</h6>
                                            <p class="text-muted mb-0">Professional blue design for business use</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Information -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewPhoto()">
                                <img id="photoPreview" class="photo-preview mt-2" style="display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Child ID specific fields -->
                    <div id="child_fields" class="type-specific form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-child me-2"></i>Child Information</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="parent_guardian" class="form-label">Parent/Guardian</label>
                                <input type="text" class="form-control" id="parent_guardian" name="parent_guardian">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="parent_phone" class="form-label">Parent Phone</label>
                                <input type="tel" class="form-control" id="parent_phone" name="parent_phone">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="height" class="form-label">Height</label>
                                <input type="text" class="form-control" id="height" name="height" placeholder="e.g., 4'8&quot;">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="weight" class="form-label">Weight</label>
                                <input type="text" class="form-control" id="weight" name="weight" placeholder="e.g., 48LBS">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="eye_color" class="form-label">Eye Color</label>
                                <input type="text" class="form-control" id="eye_color" name="eye_color">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="hair_color" class="form-label">Hair Color</label>
                                <input type="text" class="form-control" id="hair_color" name="hair_color">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type</label>
                                <select class="form-select" id="blood_type" name="blood_type">
                                    <option value="">Select Blood Type</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="medical_conditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Dog specific fields -->
                    <div id="service_dog_fields" class="type-specific form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-dog me-2"></i>Service Dog Information</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="animal_name" class="form-label">Animal's Name</label>
                                <input type="text" class="form-control" id="animal_name" name="animal_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="handler_name" class="form-label">Handler's Name</label>
                                <input type="text" class="form-control" id="handler_name" name="handler_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registry_number" class="form-label">Registry Number</label>
                                <input type="text" class="form-control" id="registry_number" name="registry_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_type" class="form-label">Service Type</label>
                                <input type="text" class="form-control" id="service_type" name="service_type">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Contacts -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Emergency Contacts</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_1_name" class="form-label">Emergency Contact 1 - Name</label>
                                <input type="text" class="form-control" id="emergency_contact_1_name" name="emergency_contact_1_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_1_phone" class="form-label">Emergency Contact 1 - Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_1_phone" name="emergency_contact_1_phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_2_name" class="form-label">Emergency Contact 2 - Name</label>
                                <input type="text" class="form-control" id="emergency_contact_2_name" name="emergency_contact_2_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_2_phone" class="form-label">Emergency Contact 2 - Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_2_phone" name="emergency_contact_2_phone">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Additional Information</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-plus me-2"></i>Create ID Card
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewPhoto() {
            const file = document.getElementById('photo').files[0];
            const preview = document.getElementById('photoPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
        
        function selectColor(color) {
            // Remove selected class from all color options
            document.querySelectorAll('.color-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelectorAll('.color-preview').forEach(preview => {
                preview.classList.remove('selected');
            });
            
            // Add selected class to chosen color
            document.querySelector(`#color_${color}`).checked = true;
            document.querySelector(`#preview_${color}`).classList.add('selected');
            document.querySelector(`#preview_${color}`).closest('.color-option').classList.add('selected');
        }
        
        function toggleFields() {
            const cardType = document.querySelector('input[name="card_type"]:checked').value;
            
            // Hide all type-specific sections
            document.querySelectorAll('.type-specific').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show relevant section
            if (cardType === 'child_id') {
                document.getElementById('child_fields').classList.add('active');
            } else if (cardType === 'service_dog') {
                document.getElementById('service_dog_fields').classList.add('active');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            // Default to red color
            selectColor('red');
        });
    </script>
</body>
</html>