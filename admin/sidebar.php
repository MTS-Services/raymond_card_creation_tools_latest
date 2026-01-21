<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)); min-height: 100vh; color: white;">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="../logo.png" alt="ShieldID" style="width: 60px; height: 40px;">
            <h5 class="mt-2">ShieldID Admin</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" 
                   href="dashboard.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'cards.php' ? 'active' : '' ?>" 
                   href="cards.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-id-card me-2"></i>Manage Cards
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'create-card.php' ? 'active' : '' ?>" 
                   href="../New_Cards/card_creation_image.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-plus-circle me-2"></i>Create New Card
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                   href="../New_cards/view_all_cards.php" target="_blank" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-eye me-2"></i>View All Cards
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'templates.php' ? 'active' : '' ?>" 
                   href="templates.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-layer-group me-2"></i>Card Templates
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" 
                   href="users.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-users me-2"></i>Admin Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>" 
                   href="settings.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>" 
                   href="reports.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-warning" href="logout.php" style="padding: 12px 20px; border-radius: 8px; margin: 2px 10px; transition: all 0.3s ease;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
        
        <!-- User Info -->
        <div class="mt-4 p-3 bg-light bg-opacity-10 rounded" style="margin: 10px;">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-2">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <small class="text-white-50">Logged in as:</small><br>
                    <strong class="text-white"><?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin' ?></strong>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    color: white !important;
    background: rgba(255,255,255,0.1) !important;
    transform: translateX(5px);
}

.sidebar .nav-link {
    transition: all 0.3s ease !important;
}

.sidebar .nav-link:hover {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.sidebar {
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar .text-warning:hover {
    background: rgba(255,193,7,0.2) !important;
    color: #ffc107 !important;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .sidebar {
        min-height: auto !important;
    }
    
    .sidebar .position-sticky {
        position: relative !important;
    }
}
</style>
