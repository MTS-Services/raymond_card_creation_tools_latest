<?php
require_once '../Middleware/Authentication.php';
require_once '../config/database.php';

new Authentication;

$card_id = (int)($_GET['id'] ?? 0);

if (!$card_id) {
    header('Location: cards.php');
    exit;
}

// Get card details
$stmt = $pdo->prepare("SELECT * FROM id_cards WHERE id = ?");
$stmt->execute([$card_id]);
$card = $stmt->fetch();

if (!$card) {
    header('Location: cards.php');
    exit;
}

$success = '';
$error = '';

// Handle form submission
if ($_POST) {
    try {
        // Handle photo upload
        $photo_path = $card['photo']; // Keep existing photo by default
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $upload_dir = '../uploads/photos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_filename = $card['card_number'] . '_' . time() . '.' . $file_extension;
            $new_photo_path = $upload_dir . $photo_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $new_photo_path)) {
                // Delete old photo if it exists
                if ($photo_path && file_exists('../' . $photo_path)) {
                    unlink('../' . $photo_path);
                }
                $photo_path = 'uploads/photos/' . $photo_filename;
            }
        }
        
        // Update card data
        $update_data = [
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
            'status' => $_POST['status'],
            'expiry_date' => $_POST['expiry_date'] ?: null,
            'id' => $card_id
        ];
        
        // Update database
        $sql = "UPDATE id_cards SET 
                full_name = :full_name, photo = :photo, date_of_birth = :date_of_birth,
                address = :address, phone = :phone, email = :email,
                parent_guardian = :parent_guardian, parent_phone = :parent_phone,
                height = :height, weight = :weight, eye_color = :eye_color, hair_color = :hair_color,
                blood_type = :blood_type, allergies = :allergies, medical_conditions = :medical_conditions,
                animal_name = :animal_name, handler_name = :handler_name, registry_number = :registry_number,
                service_type = :service_type, emergency_contact_1_name = :emergency_contact_1_name,
                emergency_contact_1_phone = :emergency_contact_1_phone, emergency_contact_2_name = :emergency_contact_2_name,
                emergency_contact_2_phone = :emergency_contact_2_phone, notes = :notes,
                status = :status, expiry_date = :expiry_date
                WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute($update_data);
        
        $success = "ID Card updated successfully!";
        
        // Refresh card data
        $stmt = $pdo->prepare("SELECT * FROM id_cards WHERE id = ?");
        $stmt->execute([$card_id]);
        $card = $stmt->fetch();
        
    } catch (Exception $e) {
        $error = "Error updating card: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Card - <?= htmlspecialchars($card['full_name']) ?></title>
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
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            padding: 15px 25px;
            border-radius: 15px 15px 0 0;
            margin: -1px -1px 20px -1px;
        }
        
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="background: #f8f9fa; min-height: 100vh;">
                <div class="pt-3 pb-2 mb-3">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2">Edit ID Card</h1>
                        <div>
                            <a href="view-card.php?id=<?= $card['id'] ?>" class="btn btn-success me-2">
                                <i class="fas fa-eye me-2"></i>View Card
                            </a>
                            <a href="cards.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cards
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <!-- Card Info -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Card Information</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Card Number</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($card['card_number']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Card Type</label>
                                    <input type="text" class="form-control" value="<?= ucfirst(str_replace('_', ' ', $card['card_type'])) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?= $card['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $card['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="expired" <?= $card['status'] == 'expired' ? 'selected' : '' ?>>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= $card['expiry_date'] ?>">
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
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($card['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= $card['date_of_birth'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($card['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($card['email'] ?? '') ?>">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($card['address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewPhoto()">
                                <?php if (!empty($card['photo'])): ?>
                                    <img src="../<?= htmlspecialchars($card['photo']) ?>" id="photoPreview" class="photo-preview mt-2">
                                <?php else: ?>
                                    <img id="photoPreview" class="photo-preview" style="display: none;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Type-specific fields would go here - similar to create-card.php -->
                    
                    <!-- Emergency Contacts -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Emergency Contacts</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_1_name" class="form-label">Emergency Contact 1 - Name</label>
                                <input type="text" class="form-control" id="emergency_contact_1_name" name="emergency_contact_1_name" value="<?= htmlspecialchars($card['emergency_contact_1_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_1_phone" class="form-label">Emergency Contact 1 - Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_1_phone" name="emergency_contact_1_phone" value="<?= htmlspecialchars($card['emergency_contact_1_phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="form-section p-4">
                        <div class="section-header">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Additional Information</h5>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($card['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Hidden fields for card type specific data -->
                    <input type="hidden" name="parent_guardian" value="<?= htmlspecialchars($card['parent_guardian'] ?? '') ?>">
                    <input type="hidden" name="parent_phone" value="<?= htmlspecialchars($card['parent_phone'] ?? '') ?>">
                    <input type="hidden" name="height" value="<?= htmlspecialchars($card['height'] ?? '') ?>">
                    <input type="hidden" name="weight" value="<?= htmlspecialchars($card['weight'] ?? '') ?>">
                    <input type="hidden" name="eye_color" value="<?= htmlspecialchars($card['eye_color'] ?? '') ?>">
                    <input type="hidden" name="hair_color" value="<?= htmlspecialchars($card['hair_color'] ?? '') ?>">
                    <input type="hidden" name="blood_type" value="<?= htmlspecialchars($card['blood_type'] ?? '') ?>">
                    <input type="hidden" name="allergies" value="<?= htmlspecialchars($card['allergies'] ?? '') ?>">
                    <input type="hidden" name="medical_conditions" value="<?= htmlspecialchars($card['medical_conditions'] ?? '') ?>">
                    <input type="hidden" name="animal_name" value="<?= htmlspecialchars($card['animal_name'] ?? '') ?>">
                    <input type="hidden" name="handler_name" value="<?= htmlspecialchars($card['handler_name'] ?? '') ?>">
                    <input type="hidden" name="registry_number" value="<?= htmlspecialchars($card['registry_number'] ?? '') ?>">
                    <input type="hidden" name="service_type" value="<?= htmlspecialchars($card['service_type'] ?? '') ?>">
                    <input type="hidden" name="emergency_contact_2_name" value="<?= htmlspecialchars($card['emergency_contact_2_name'] ?? '') ?>">
                    <input type="hidden" name="emergency_contact_2_phone" value="<?= htmlspecialchars($card['emergency_contact_2_phone'] ?? '') ?>">
                    
                    <!-- Submit Button -->
                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update ID Card
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
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>