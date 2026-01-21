<?php
session_start();
require_once 'config/database.php';

// Initialize database
initializeDatabase();

// Fetch all ID cards from database
try {
    $stmt = $pdo->query("SELECT * FROM id_cards ORDER BY created_at DESC");
    $idCards = $stmt->fetchAll();
} catch(PDOException $e) {
    $idCards = [];
    error_log("Error fetching ID cards: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShieldID - Virtual ID System</title>
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
    
    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <!-- Swiper.js for Mobile Slider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Responsive Card Styles -->
    <style>
      
      .send_us{
      background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)) !important;
        color:white;
      }
      body{
      padding:0px !important;
      }
        /* Mobile Card Styles - Smaller cards on mobile */
        @media (max-width: 767.98px) {
            .product-card {
                max-width: 100% !important;
                width: 100% !important;
            }
            
            .product-image {
                height: 120px !important;
            }
            
            .product-info {
                padding: 10px !important;
            }
            
            .product-title {
                font-size: 0.75rem !important;
                margin-bottom: 6px !important;
            }
            
            .coming-soon-text {
                font-size: 0.7rem !important;
                margin-bottom: 8px !important;
            }
            
            .product-info .btn-primary {
                padding: 8px !important;
                font-size: 0.75rem !important;
            }
            
            .product-info .btn-primary i {
                font-size: 0.9rem !important;
            }
        }
        
        /* Extra Small Mobile (very small screens) */
        @media (max-width: 575.98px) {
            .product-image {
                height: 100px !important;
            }
            
            .product-info {
                padding: 8px !important;
            }
            
            .product-title {
                font-size: 0.7rem !important;
            }
        }
        
        /* Swiper Pagination Styles for Features Section */
        .featuresSwiper .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
            width: 10px;
            height: 10px;
        }
        
        .featuresSwiper .swiper-pagination-bullet-active {
            background: #fff;
            width: 12px;
            height: 12px;
        }
        
        .featuresSwiper .swiper-pagination {
            bottom: 10px;
        }
      .fixed-top{
      	position:sticky !important;
      }
    </style>
</head>
<body>
    <!-- Professional Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <!-- Mobile Layout: Menu (left) | Logo (center) | Search & Cart (right) -->
            <div class="d-flex justify-content-between align-items-center w-100 d-lg-none">
                <!-- Menu Icon (Left) -->
                <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: none; box-shadow: none;">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Logo (Center) -->
                <a class="navbar-brand d-flex align-items-center mx-auto" href="index.php" style="position: absolute; left: 50%; transform: translateX(-50%);">
                    <img src="logo.png" 
                         alt="ShieldID Logo" 
                         style="width: 120px; height: 75px;">
                </a>
                
                <!-- Search & Cart Icons (Right) -->
                <div class="d-flex align-items-center" style="z-index: 10; gap: 8px;">
                    <!-- Search Icon -->
                    <button class="btn border-0 p-2" type="button" id="mobileSearchToggle" style="background: none; color: #333;">
                        <i class="fas fa-search" style="font-size: 1.2rem;"></i>
                    </button>
                    <!-- Cart Icon -->
                    <a class="btn border-0 p-2 position-relative" style="background: none; color: #333; margin-left: 4px;">
                        <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 2px 5px;">
                            0
                        </span>
                    </a>
                </div>
            </div>
            
            <!-- Desktop Layout -->
            <a class="navbar-brand d-none d-lg-flex align-items-center" href="index.php" style="padding: 8px 0;">
                <img src="logo.png" 
                     alt="ShieldID Logo" 
                     style="width: 120px; height: 75px;">
            </a>
            
            <!-- Mobile Search Bar (Hidden by default) -->
            <div class="w-100 d-lg-none mb-2 mt-2" id="mobileSearchBar" style="display: none; transition: all 0.3s ease;">
                <form id="mobileIdLookupForm">
                    <div class="input-group" style="margin-top:20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 25px; overflow: hidden;">
                        <input type="text" id="mobileCardIdInput" class="form-control" placeholder="ID Lookup..." style="border: none; padding: 12px 20px; background: #f8f9fa; font-size: 0.9rem;" required>
                        <button class="btn" type="submit" style="background: linear-gradient(135deg, #2c5aa0, #1e3d72); border: none; padding: 12px 18px; color: white;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 text-dark" href="#home">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 text-dark" href="#templates">
                            Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 text-dark" href="#about_us2">
                            About us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 text-dark" href="#" data-bs-toggle="modal" data-bs-target="#contactModal">
                            Contact
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Desktop Search Bar -->
                    <li class="nav-item me-3 d-none d-lg-block">
                        <form id="idLookupForm" style="display: flex;">
                            <div class="input-group" style="width: 280px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 25px; overflow: hidden;">
                                <input type="text" id="cardIdInput" class="form-control" placeholder="ID Lookup..." style="border: none; padding: 12px 20px; background: #f8f9fa; font-size: 0.9rem;" required>
                                <button class="btn" type="submit" style="background: linear-gradient(135deg, #2c5aa0, #1e3d72); border: none; padding: 12px 18px; color: white;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                    <!-- Cart Icon -->
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link position-relative" style="color: white !important; background: linear-gradient(135deg, #2c5aa0, #1e3d72); padding: 10px 14px; border-radius: 8px; box-shadow: 0 2px 8px rgba(44, 90, 160, 0.3);">
                            <i class="fas fa-shopping-cart" style="font-size: 1.2rem;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                                0
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-10">
                    <div class="hero-content">
                        
                        
                        <div class="hero-actions mt-5">
                            <a href="#templates" class="btn btn-shop">SHOP NOW</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Templates Section -->
    <section id="templates" class="templates-section py-5">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5">
                <p class="section-badge mb-2">BEST SELLERS</p>
                <h2 class="section-title mb-4">ALL ID Cards</h2>
            </div>

            <?php
            // Static images from root folder
            $display_images = ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg', '5.jpeg', '6.jpeg'];
            ?>

            <?php if (empty($display_images)): ?>
                <!-- No Images Message -->
                <div class="text-center py-5">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>No Images Available</h5>
                        <p>No images found in the New Cards folder. Please add some images to display here.</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Image Grid - 4 columns per row on desktop, 2 on mobile -->
                <div class="row g-3 g-md-4 mb-5">
                    <?php foreach ($display_images as $index => $image): ?>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="product-card" style="width: 100%; max-width: 250px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                <div class="product-image" style="width: 100%; height: 180px; overflow: hidden;">
                                    <img src="<?= htmlspecialchars($image) ?>" 
                                         alt="ID Card Template <?= $index + 1 ?>" 
                                         class="card-image"
                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                </div>
                                <div class="product-info" style="padding: 15px; text-align: center;">
                                    <?php
                                    // Define card names based on index
                                    $card_names = [
                                        'Service Dog Card/Handler (Blue/Red)',
                                        'Child Identification Card (Blue)',
                                        'Emergency ID Card',
                                        'Autism Awareness Card',
                                        'Child Identification Card (Red)',
                                        'Emotional Support Cat Card'
                                    ];
                                    $card_name = isset($card_names[$index]) ? $card_names[$index] : 'ID CARD TEMPLATE ' . ($index + 1);
                                    ?>
                                    <h6 class="product-title" style="margin-bottom: 8px; font-size: 0.9rem; font-weight: 600; color: #333; line-height: 1.3;"><?= htmlspecialchars($card_name) ?></h6>
                                    <div class="coming-soon-text" style="margin-bottom: 12px; font-size: 0.8rem; color: #ff6b35; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <i class="fas fa-clock me-1"></i>Coming Soon
                                    </div>
                                    <button class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.9rem; font-weight: 600; border-radius: 8px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); color: white; border: none; transition: all 0.3s ease;">
                                        <i class="fas fa-shopping-cart me-2" style="font-size: 1.1rem;"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- View All Button -->
            <div class="text-center">
                <button class="btn btn-view-all">VIEW ALL</button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5" id="features-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <!-- Desktop Layout - Grid (hidden on mobile) -->
            <div class="row text-center d-none d-md-flex">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon mb-3">
                            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                <i class="fas fa-hand-paper text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 class="text-white">EASY PRINT ON DEMAND</h5>
                        <h6 class="text-white-50">ID SERVICES</h6>
                        <p class="text-white-50 small">Personalize professional ID cards with our easy-to-use platform.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon mb-3">
                            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                <i class="fas fa-shipping-fast text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 class="text-white">FAST SHIPPING AND</h5>
                        <h6 class="text-white-50">FULFILLMENT</h6>
                        <p class="text-white-50 small">Quick processing and fast shipping to get your ID cards when you need them.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon mb-3">
                            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                <i class="fas fa-calculator text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 class="text-white">NO ORDER MINIMUMS</h5>
                        <h6 class="text-white-50">LOW QUANTITIES</h6>
                        <p class="text-white-50 small">Order as few or as many ID cards as you need - no minimum quantities required.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon mb-3">
                            <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                <i class="fas fa-th-large text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h5 class="text-white">50+ PRE-DESIGNED</h5>
                        <h6 class="text-white-50">Cards</h6>
                        <p class="text-white-50 small">Choose from over 50 professionally designed cards for every need.</p>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Layout - Swiper Slider (hidden on desktop) -->
            <div class="swiper featuresSwiper d-md-none" style="padding-bottom: 40px;">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                    <i class="fas fa-hand-paper text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="text-white">EASY PRINT ON DEMAND</h5>
                            <h6 class="text-white-50">ID SERVICES</h6>
                            <p class="text-white-50 small">Personalize professional ID cards with our easy-to-use platform.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                    <i class="fas fa-shipping-fast text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="text-white">FAST SHIPPING AND</h5>
                            <h6 class="text-white-50">FULFILLMENT</h6>
                            <p class="text-white-50 small">Quick processing and fast shipping to get your ID cards when you need them.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                    <i class="fas fa-calculator text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="text-white">NO ORDER MINIMUMS</h5>
                            <h6 class="text-white-50">LOW QUANTITIES</h6>
                            <p class="text-white-50 small">Order as few or as many ID cards as you need - no minimum quantities required.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="feature-item text-center">
                            <div class="feature-icon mb-3">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: linear-gradient(135deg, #2c5aa0, #1e3d72); border-radius: 50%;">
                                    <i class="fas fa-th-large text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <h5 class="text-white">50+ PRE-DESIGNED</h5>
                            <h6 class="text-white-50">Cards</h6>
                            <p class="text-white-50 small">Choose from over 50 professionally designed cards for every need.</p>
                        </div>
                    </div>
                </div>
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>


        
    </section>

    <!-- Search Section -->

    <!-- Verify Section -->



    <!-- Contact Form Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #2c5aa0, #1e3d72); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="order">Order Support</option>
                                <option value="technical">Technical Support</option>
                                <option value="custom">Custom ID Card Request</option>
                                <option value="billing">Billing Question</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Please describe your inquiry in detail..." required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Subscribe to our newsletter for updates and special offers
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn send_us" onclick="submitContactForm()">
                       Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ID Display Modal -->
    <div class="modal fade" id="idModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generated Virtual ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="idModalBody">
                    <!-- ID will be generated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadId()">Download ID</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Enhanced Trust Indicators -->
    <section class="py-5" style="background: linear-gradient(135deg, #1e3d72, #2c5aa0); position: relative;">
        <!-- Background Pattern -->
        <div class="position-absolute w-100 h-100" style="top: 0; left: 0; opacity: 0.1; background-image: radial-gradient(circle at 25% 25%, white 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container position-relative">
            <div class="text-center mb-4">
                <h3 class="text-white fw-bold mb-2">Why Choose ShieldID?</h3>
                <p class="text-white-50">Professional virtual ID services you can trust</p>
            </div>
            
            <div class="row text-center text-white">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="trust-item p-4">
                        <div class="trust-icon mb-3">
                            <i class="fas fa-bolt fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Instant Processing</h5>
                        <p class="text-white-50 mb-0">48h order dispatch</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="trust-item p-4">
                        <div class="trust-icon mb-3">
                            <i class="fas fa-shield-alt fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Durability</h5>
                        <p class="text-white-50 mb-0">Laminated ID Cards</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="trust-item p-4">
                        <div class="trust-icon mb-3">
                            <i class="fas fa-qrcode fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Easy Scan QR</h5>
                        <p class="text-white-50 mb-0">Virtual ID included with eligible cards</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="trust-item p-4">
                        <div class="trust-icon mb-3">
                            <i class="fas fa-headset fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-2">24h Response Time</h5>
                        <p class="text-white-50 mb-0">Reliable customer assistance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5" id="about_us2">
        <div class="container">
            <div class="row">
                <!-- Newsletter Section -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">NEWSLETTER</h5>
                    <p class="text-white-50 small mb-3">Sign up to our newsletter to receive exclusive offers and news.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email" style="border-radius: 25px 0 0 25px;">
                        <button class="btn btn-warning" type="button" style="border-radius: 0 25px 25px 0;">SUBSCRIBE</button>
                    </div>
                </div>
                
                <!-- Quick Links Section -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#templates" class="text-white-50 text-decoration-none">Shop</a></li>
                        <li class="mb-2"><a href="#about_us2" class="text-white-50 text-decoration-none">About us</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                    </ul>
                </div>
                
                <!-- About Section -->
                <div class="col-lg-6 col-md-12 mb-4">
                    <h6 class="mb-3">ABOUT SHIELDID</h6>
                    <p class="text-white-50 small">Shield ID is a family-owned business in Allentown, Pennsylvania. We specialize in custom safety and awareness products, delivering top-quality results with quick turnaround times. Get in touch or explore our services to learn more.</p>
                </div>
            </div>
            
            <hr class="border-secondary my-4">
            
            <!-- Bottom Section -->
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-white-50 small mb-0">© 2025 - ShieldID LLC, All Rights Reserved.</p>
                </div>
                <div class="col-md-6">
                    <div class="text-md-end text-center">
                        <!-- Payment Icons -->
                        <i class="fab fa-cc-paypal fa-2x me-2 text-white-50"></i>
                        <i class="fab fa-cc-visa fa-2x me-2 text-white-50"></i>
                        <i class="fab fa-cc-mastercard fa-2x me-2 text-white-50"></i>
                        <i class="fab fa-cc-stripe fa-2x me-2 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- HTML2Canvas for PDF generation -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <!-- jsPDF for PDF generation -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <!-- Custom JS -->
    <!-- <script src="assets/js/main.js"></script> -->
    
    <script>
        // Add hover effects for product cards
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                    
                    const img = this.querySelector('img');
                    if (img) {
                        img.style.transform = 'scale(1.05)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                    
                    const img = this.querySelector('img');
                    if (img) {
                        img.style.transform = 'scale(1)';
                    }
                });
            });
        });

        // Mobile Search Bar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileSearchToggle = document.getElementById('mobileSearchToggle');
            const mobileSearchBar = document.getElementById('mobileSearchBar');
            
            if (mobileSearchToggle && mobileSearchBar) {
                mobileSearchToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle the search bar visibility with smooth animation
                    if (mobileSearchBar.style.display === 'none' || !mobileSearchBar.style.display || mobileSearchBar.style.display === '') {
                        mobileSearchBar.style.display = 'block';
                        mobileSearchBar.style.opacity = '0';
                        mobileSearchBar.style.transform = 'translateY(-10px)';
                        
                        // Animate in
                        setTimeout(function() {
                            mobileSearchBar.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            mobileSearchBar.style.opacity = '1';
                            mobileSearchBar.style.transform = 'translateY(0)';
                        }, 10);
                        
                        // Focus on the input field
                        setTimeout(function() {
                            const input = document.getElementById('mobileCardIdInput');
                            if (input) {
                                input.focus();
                            }
                        }, 300);
                    } else {
                        // Animate out
                        mobileSearchBar.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        mobileSearchBar.style.opacity = '0';
                        mobileSearchBar.style.transform = 'translateY(-10px)';
                        
                        setTimeout(function() {
                            mobileSearchBar.style.display = 'none';
                        }, 300);
                    }
                });
                
                // Close search bar when clicking outside (optional)
                document.addEventListener('click', function(e) {
                    if (mobileSearchBar && mobileSearchBar.style.display === 'block') {
                        if (!mobileSearchBar.contains(e.target) && e.target !== mobileSearchToggle && !mobileSearchToggle.contains(e.target)) {
                            mobileSearchBar.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            mobileSearchBar.style.opacity = '0';
                            mobileSearchBar.style.transform = 'translateY(-10px)';
                            
                            setTimeout(function() {
                                mobileSearchBar.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            }
        });

        // Desktop ID Lookup form submission
        const desktopIdLookupForm = document.getElementById('idLookupForm');
        if (desktopIdLookupForm) {
            desktopIdLookupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const cardId = document.getElementById('cardIdInput').value.trim();
                
                if (cardId) {
                    // Redirect to the card view page
                    window.location.href = `New_cards/view_card.php?id=${encodeURIComponent(cardId)}`;
                } else {
                    alert('Please enter a card ID to search.');
                }
            });
        }

        // Mobile ID Lookup form submission
        const mobileIdLookupForm = document.getElementById('mobileIdLookupForm');
        if (mobileIdLookupForm) {
            mobileIdLookupForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const cardId = document.getElementById('mobileCardIdInput').value.trim();
                
                if (cardId) {
                    // Redirect to the card view page
                    window.location.href = `New_cards/view_card.php?id=${encodeURIComponent(cardId)}`;
                } else {
                    alert('Please enter a card ID to search.');
                }
            });
        }

        // Initialize Swiper for Features Section (Mobile Only)
        document.addEventListener('DOMContentLoaded', function() {
            let featuresSwiper = null;
            
            function initFeaturesSwiper() {
                // Only initialize on mobile screens
                if (window.innerWidth < 768 && !featuresSwiper) {
                    const swiperElement = document.querySelector('.featuresSwiper');
                    if (swiperElement) {
                        featuresSwiper = new Swiper('.featuresSwiper', {
                            slidesPerView: 1,
                            spaceBetween: 30,
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                                dynamicBullets: true,
                            },
                            autoplay: {
                                delay: 4000,
                                disableOnInteraction: false,
                            },
                            loop: true,
                        });
                    }
                }
            }
            
            function destroyFeaturesSwiper() {
                if (featuresSwiper && window.innerWidth >= 768) {
                    featuresSwiper.destroy(true, true);
                    featuresSwiper = null;
                }
            }
            
            // Initialize on load
            initFeaturesSwiper();
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 768) {
                        destroyFeaturesSwiper();
                    } else {
                        initFeaturesSwiper();
                    }
                }, 250);
            });
        });

        // Contact form submission
        function submitContactForm() {
            const form = document.getElementById('contactForm');
            const formData = new FormData(form);
            
            // Basic validation
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            if (!firstName || !lastName || !email || !subject || !message) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }
            
            // Simulate form submission (you can replace this with actual form handling)
            alert('Thank you for your message! We will get back to you soon.');
            
            // Reset form
            form.reset();
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
            modal.hide();
        }
    </script>
</body>
</html>