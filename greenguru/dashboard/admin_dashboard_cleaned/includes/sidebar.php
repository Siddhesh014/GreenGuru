<div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu" style="background: #1e293b; box-shadow: 4px 0 10px rgba(0,0,0,0.05);">
    <div class="position-sticky pt-3 vh-100 flex-column d-flex">
        <!-- Brand Logo -->
        <div class="text-center mb-5 px-3">
            <a href="index.php" class="d-flex align-items-center justify-content-center text-decoration-none">
                <div class="sidebar-logo bg-primary rounded-3 p-2 me-2 shadow-sm">
                    <i class="bi bi-leaf-fill text-white fs-4"></i>
                </div>
                <span class="fs-5 fw-bold text-white ls-1">Green<span class="text-primary">Guru</span></span>
            </a>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav flex-column px-3 flex-grow-1">
            <?php
            // Calculate base path relative to current script
            $current_dir = basename(dirname($_SERVER['PHP_SELF']));
            $is_sub = ($current_dir != 'admin_dashboard_cleaned');
            $prefix = $is_sub ? '../' : '';
            ?>
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center <?php echo (!$is_sub && basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo $prefix; ?>index.php">
                    <i class="bi bi-grid-1x2-fill me-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center <?php echo (strpos($_SERVER['PHP_SELF'], 'products/') !== false) ? 'active' : ''; ?>" href="<?php echo $prefix; ?>products/index.php">
                    <i class="bi bi-box-seam-fill me-3"></i>
                    <span>Products</span>
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center <?php echo (strpos($_SERVER['PHP_SELF'], 'orders/') !== false) ? 'active' : ''; ?>" href="<?php echo $prefix; ?>orders/index.php">
                    <i class="bi bi-receipt-cutoff me-3"></i>
                    <span>Orders</span>
                    <span class="badge bg-primary ms-auto rounded-pill">3</span>
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center <?php echo (strpos($_SERVER['PHP_SELF'], 'users/') !== false) ? 'active' : ''; ?>" href="<?php echo $prefix; ?>users/index.php">
                    <i class="bi bi-people-fill me-3"></i>
                    <span>Customers</span>
                </a>
            </li>

            <li class="nav-item mt-4 mb-2 small text-muted text-uppercase fw-bold ls-1">System</li>
            
            <li class="nav-item mb-2">
                <a class="nav-link d-flex align-items-center <?php echo (strpos($_SERVER['PHP_SELF'], 'settings/') !== false) ? 'active' : ''; ?>" href="<?php echo $prefix; ?>settings/index.php">
                    <i class="bi bi-gear-fill me-3"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <!-- Bottom Section -->
        <div class="p-3 mt-auto border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center text-white mb-4 px-2">
                <img src="https://ui-avatars.com/api/?name=Admin&background=0d6efd&color=fff&bold=true" 
                     class="rounded-circle border border-2 border-primary me-3" width="40" height="40" alt="Admin">
                <div class="overflow-hidden">
                    <div class="fw-bold small text-truncate">Administrator</div>
                    <div class="smaller text-muted">@greenguru</div>
                </div>
            </div>
            <div class="d-grid gap-2">
                <a href="<?php echo $prefix; ?>../../Home page/index.php" class="btn btn-sm btn-outline-light border-secondary border-opacity-50 rounded-pill">
                    <i class="bi bi-house-door me-2"></i>Storefront
                </a>
                <a href="<?php echo $prefix; ?>../../Login page/logout.php" class="btn btn-sm btn-danger rounded-pill">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar { min-height: 100vh; color: #94a3b8; }
    .nav-link { 
        color: #94a3b8; 
        padding: 0.8rem 1rem; 
        border-radius: 12px; 
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
    .nav-link.active { background: #0d6efd; color: #fff; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3); }
    .nav-link i { font-size: 1.1rem; }
    .ls-1 { letter-spacing: 0.05rem; }
    .smaller { font-size: 0.7rem; }
    
    @media (max-width: 767.98px) {
        .sidebar { position: fixed; top: 0; left: 0; z-index: 1000; height: 100%; width: 260px; }
    }
</style>
