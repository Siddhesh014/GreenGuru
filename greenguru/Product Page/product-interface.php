<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login page/login.html");
    exit();
}
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);
if ($isAdminSession) {
    // Admin should not see product page
    header("Location: ../dashboard/admin_dashboard_cleaned/index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenGURU - Eco-Friendly Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static@latest/font/lucide.css">
    <style>
        :root {
            /* Color system */
            --primary:#4caf50;
            --primary-light: #60ad5e;
            --primary-dark: #005005;
            --secondary: #263238;
            --secondary-light: #4f5b62;
            --secondary-dark: #000a12;
            --accent: #ff6d00;
            --accent-light: #ff9e40;
            --accent-dark: #c43c00;
            --success: #4caf50;
            --warning: #ff9800;
            --error: #f44336;
            --background: #ffffff;
            --surface: #f8f9fa;
            --surface-variant: #f0f4f1;
            --text-primary: #212121;
            --text-secondary: #424242;
            --text-tertiary: #757575;
            --text-on-primary: #ffffff;
            --text-on-secondary: #ffffff;
            --border-light: rgba(0, 0, 0, 0.08);
            --border-medium: rgba(0, 0, 0, 0.12);
            
            /* Spacing system */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            --space-3xl: 4rem;
            
            /* Typography */
            --font-heading: 'Montserrat', sans-serif;
            --font-body: 'Poppins', sans-serif;
            
            /* Border radius */
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 12px 24px rgba(0, 0, 0, 0.12);
            
            /* Transitions */
            --transition-fast: 150ms ease;
            --transition-normal: 250ms ease;
            --transition-slow: 350ms ease;
            
            /* Container widths */
            --container-sm: 640px;
            --container-md: 768px;
            --container-lg: 1024px;
            --container-xl: 1280px;
        }

        /* Reset & Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            line-height: 1.6;
            color: var(--text-primary);
            background: linear-gradient(to bottom, var(--background) 0%, var(--surface-variant) 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: var(--space-md);
            color: var(--text-primary);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        h2 {
            font-size: 2rem;
            letter-spacing: -0.3px;
        }

        h3 {
            font-size: 1.5rem;
        }

        p {
            margin-bottom: var(--space-md);
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: color var(--transition-normal);
        }

        a:hover {
            color: var(--primary-dark);
        }

        img {
            max-width: 100%;
            height: auto;
        }

        button {
            cursor: pointer;
            font-family: var(--font-body);
        }

        /* Layout */
        .container {
            width: 100%;
            max-width: var(--container-xl);
            margin: 0 auto;
            padding: 0 var(--space-lg);
        }

        .section {
            padding: var(--space-2xl) 0;
        }

        /* Header & Navigation */
        .header {
            background-color: var(--primary);
            color: var(--text-on-primary);
            padding: var(--space-md) 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-md);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-on-primary);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo i {
            font-size: 1.75rem;
        }

        .nav-links {
            display: flex;
            gap: var(--space-lg);
            align-items: center;
        }

        .nav-link {
            color: var(--text-on-primary);
            font-weight: 500;
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            transition: background-color var(--transition-normal);
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: var(--text-on-primary);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        /* Page Header */
        .page-header {
            text-align: center;
            margin-bottom: var(--space-2xl);
        }

        .page-title {
            margin-bottom: var(--space-sm);
            position: relative;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            border-radius: 2px;
        }

        .page-description {
            color: var(--text-secondary);
            max-width: 800px;
            margin: var(--space-lg) auto 0;
            font-size: 1.1rem;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: var(--space-2xl);
        }

        .search-bar {
            display: flex;
            gap: var(--space-sm);
            background-color: var(--background);
            padding: var(--space-md);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .search-input {
            flex-grow: 1;
            padding: var(--space-md) var(--space-lg);
            font-size: 1rem;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            transition: border-color var(--transition-normal), box-shadow var(--transition-normal);
            font-family: var(--font-body);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
        }

        .search-input::placeholder {
            color: var(--text-tertiary);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-md) var(--space-lg);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: var(--radius-md);
            border: none;
            transition: all var(--transition-normal);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--text-on-primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: var(--text-on-secondary);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-accent {
            background-color: var(--accent);
            color: var(--text-on-primary);
        }

        .btn-accent:hover {
            background-color: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary);
            color: var(--text-on-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: var(--radius-md);
            font-size: 1.25rem;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--space-xl);
        }

        .product-card {
            background-color: var(--background);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-normal), box-shadow var(--transition-normal);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .product-image-container {
            position: relative;
            overflow: hidden;
            height: 220px;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform var(--transition-slow);
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }

        .product-content {
            padding: var(--space-lg);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: var(--space-sm);
            color: var(--text-primary);
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--space-md);
            display: flex;
            align-items: center;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: var(--space-md);
        }

        .star {
            color: #ffc107;
            margin-right: 2px;
            font-size: 1rem;
        }

        .sustainability-score {
            position: absolute;
            top: var(--space-md);
            right: var(--space-md);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: var(--space-xs) var(--space-md);
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .product-actions {
            margin-top: auto;
            display: flex;
            gap: var(--space-sm);
        }

        .product-actions .btn {
            flex: 1;
        }

        /* Product Popup */
        #popupOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        #productPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1001;
            width: 90%;
            max-width: 900px;
            background-color: var(--background);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            animation: zoomIn 0.3s ease-out;
        }

        .popup-content {
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .popup-content {
                flex-direction: row;
            }
        }

        .popup-image {
            flex: 1;
            max-width: 100%;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .popup-image {
                max-width: 40%;
            }
        }

        .popup-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform var(--transition-slow);
        }

        .popup-image:hover img {
            transform: scale(1.05);
        }

        .popup-details {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: var(--space-xl);
            background-color: var(--background);
        }

        .popup-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-md);
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .popup-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--space-lg);
            display: flex;
            align-items: center;
        }

        .popup-description {
            margin-bottom: var(--space-xl);
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .popup-close {
            position: absolute;
            top: var(--space-md);
            right: var(--space-md);
            background: rgba(0, 0, 0, 0.1);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-primary);
            transition: all var(--transition-normal);
            z-index: 10;
            font-size: 1.2rem;
        }

        .popup-close:hover {
            background-color: var(--error);
            color: white;
            transform: rotate(90deg);
        }

        #popupAddToCartButton {
            width: 100%;
            padding: var(--space-md);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Cart Button */
        #myCartButton {
            position: fixed;
            bottom: var(--space-xl);
            right: var(--space-xl);
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            padding: var(--space-md) var(--space-lg);
            border: none;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-lg);
            cursor: pointer;
            transition: all var(--transition-normal);
            z-index: 99;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            letter-spacing: 0.5px;
        }

        #myCartButton:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        /* Footer */
        .footer {
            background-color: var(--secondary-dark);
            color: var(--text-on-secondary);
            padding: var(--space-2xl) 0 var(--space-lg);
            margin-top: var(--space-3xl);
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary), var(--primary-light), var(--accent));
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-xl);
        }

        .footer-section h3 {
            font-size: 1.4rem;
            margin-bottom: var(--space-lg);
            color: var(--text-on-secondary);
            position: relative;
            display: inline-block;
            padding-bottom: var(--space-sm);
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            border-radius: 3px;
        }

        .footer-section p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: var(--space-lg);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: var(--space-sm);
            position: relative;
            padding-left: 15px;
        }

        .footer-section ul li::before {
            content: '›';
            position: absolute;
            left: 0;
            color: var(--primary-light);
            font-size: 1.2rem;
            line-height: 1;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all var(--transition-normal);
            display: inline-block;
            font-size: 0.95rem;
        }

        .footer-section a:hover {
            color: var(--text-on-secondary);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: var(--space-md);
            flex-wrap: wrap;
        }

        .social-link {
            padding: var(--space-sm) var(--space-md);
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            transition: all var(--transition-normal);
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .social-link:hover {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
            color: white;
        }

        .social-link i {
            font-size: 1.1rem;
        }

        .footer-bottom {
            margin-top: var(--space-2xl);
            text-align: center;
            padding-top: var(--space-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        .footer-bottom p {
            margin-bottom: 0;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .nav-links {
                gap: var(--space-md);
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
            
            .search-bar {
                flex-direction: column;
            }
            
            .search-bar .btn {
                width: 100%;
            }
            
            #myCartButton {
                bottom: var(--space-md);
                right: var(--space-md);
                padding: var(--space-sm) var(--space-md);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 var(--space-md);
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .page-description {
                font-size: 1rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .mb-1 {
            margin-bottom: var(--space-sm);
        }
        
        .mb-2 {
            margin-bottom: var(--space-md);
        }
        
        .mb-3 {
            margin-bottom: var(--space-lg);
        }
        
        .mb-4 {
            margin-bottom: var(--space-xl);
        }
        
        .mb-5 {
            margin-bottom: var(--space-2xl);
        }
        
        .mt-1 {
            margin-top: var(--space-sm);
        }
        
        .mt-2 {
            margin-top: var(--space-md);
        }
        
        .mt-3 {
            margin-top: var(--space-lg);
        }
        
        .mt-4 {
            margin-top: var(--space-xl);
        }
        
        .mt-5 {
            margin-top: var(--space-2xl);
        }
        
        .py-1 {
            padding-top: var(--space-sm);
            padding-bottom: var(--space-sm);
        }
        
        .py-2 {
            padding-top: var(--space-md);
            padding-bottom: var(--space-md);
        }
        
        .py-3 {
            padding-top: var(--space-lg);
            padding-bottom: var(--space-lg);
        }
        
        .py-4 {
            padding-top: var(--space-xl);
            padding-bottom: var(--space-xl);
        }
        
        .py-5 {
            padding-top: var(--space-2xl);
            padding-bottom: var(--space-2xl);
        }
    </style>
</head>
<body>
    <!-- Popup Overlay and Product Popup -->
    <div id="popupOverlay"></div>
    <div id="productPopup">
        <button class="popup-close" onclick="closePopup()">✖</button>
        <div class="popup-content">
            <div class="popup-image">
                <img src="/placeholder.svg" alt="Product Image" id="popupProductImage" />
            </div>
            <div class="popup-details">
                <div>
                    <h2 class="popup-title" id="popupProductName"></h2>
                    <div class="product-rating" id="popupProductrating"></div>  
                    <div class="popup-price" id="popupProductPrice"></div>
                    <p class="popup-description" id="popupProductDescription"></p>
                </div>
                <button class="btn btn-primary" id="popupAddToCartButton">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="../index.php" class="logo">
                    <i class="lucide-leaf"></i>
                    GreenGURU
                </a>
                <div class="nav-links">
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="product-interface.php" class="nav-link active">Products</a>
                    <a href="../Cart Page/cart.php" class="nav-link">Cart</a>
                    <a href="../About page/about.html" class="nav-link">About Us</a>
                    <a href="#" class="nav-link">Contact</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container section">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Eco-Friendly Products</h1>
                <p class="page-description">Discover our curated collection of sustainable products that help reduce environmental impact and promote a greener lifestyle.</p>
            </div>
            
            <!-- Search Container -->
            <div class="search-container">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search for eco-friendly products...">
                    <button class="btn btn-accent btn-icon" id="voice-search-button" title="Voice Search">🎤</button>
                    <button class="btn btn-secondary" id="clear-button">Clear</button>
                </div>
            </div>
            
            <!-- Product Grid -->
            <div class="product-grid" id="product-grid">
                <!-- Products will be dynamically rendered here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>About GreenGURU</h3>
                    <p>We're on a mission to make sustainable living accessible and enjoyable for everyone. Our carefully curated products help reduce environmental impact while supporting ethical businesses.</p>
                    <div class="footer-logo">
                        <i class="lucide-leaf"></i>
                        <span>GreenGURU</span>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="product-interface.php">All Products</a></li>
                        <li><a href="../About page/about.html">About Us</a></li>
                        <li><a href="#">Sustainability Blog</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="#">Frequently Asked Questions</a></li>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p>Follow us on social media for sustainable living tips, product updates, and more.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="lucide-facebook"></i> Facebook</a>
                        <a href="#" class="social-link"><i class="lucide-twitter"></i> Twitter</a>
                        <a href="#" class="social-link"><i class="lucide-instagram"></i> Instagram</a>
                        <a href="#" class="social-link"><i class="lucide-linkedin"></i> LinkedIn</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 GreenGURU. All rights reserved. | Made with <span class="heart">❤</span> for the planet</p>
            </div>
        </div>
    </footer>

    <!-- Cart Button -->
    <button id="myCartButton" onclick="window.location.href='../Cart Page/cart.php?user_id=<?php echo $user_id; ?>'">
        <i class="lucide-shopping-cart"></i> My Cart
    </button>

    <!-- Scripts -->
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            // Cart button update
            function updateCartButton() {
                document.getElementById('myCartButton').innerHTML = `<i class="lucide-shopping-cart"></i> My Cart`;
            }
            
            // Add to cart functionality
            async function addToCart(productIndexNo) {
                try {
                    const response = await fetch('save_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: <?php echo $user_id; ?>,
                            product_index_no: productIndexNo,
                            quantity: 1,
                        }),
                    });

                    const result = await response.json();
                    if (result.success) {
                        // Show success notification
                        showNotification('Product added to cart!', 'success');
                    } else {
                        showNotification('Error adding product to cart: ' + result.error, 'error');
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    showNotification('Failed to add product to cart. Please try again.', 'error');
                }
            }
            
            // Notification function
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                
                // Style the notification
                Object.assign(notification.style, {
                    position: 'fixed',
                    bottom: '20px',
                    left: '20px',
                    padding: '12px 20px',
                    borderRadius: '4px',
                    color: 'white',
                    zIndex: '10000',
                    boxShadow: '0 4px 8px rgba(0,0,0,0.2)',
                    animation: 'fadeIn 0.3s, fadeOut 0.3s 2.7s',
                    backgroundColor: type === 'success' ? '#4caf50' : '#f44336'
                });
                
                document.body.appendChild(notification);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Popup functionality
            const popupOverlay = document.getElementById("popupOverlay");
            const productPopup = document.getElementById("productPopup");

            window.openPopup = function(product) {
                // Set the content for the popup
                document.getElementById("popupProductImage").src = product.image;
                document.getElementById("popupProductName").textContent = product.name;

                // Create and display star rating in the popup
                const ratingContainer = document.getElementById("popupProductrating");
                ratingContainer.innerHTML = ''; // Clear any existing content
                for (let i = 0; i < 5; i++) {
                    const star = document.createElement('span');
                    star.classList.add('star');
                    star.textContent = i < Math.round(product.rating) ? '★' : '☆';
                    ratingContainer.appendChild(star);
                }

                document.getElementById("popupProductPrice").textContent = `₹${product.price}`;
                document.getElementById("popupProductDescription").textContent = product.DESCRIPTION || 'No description available';

                // Set the Add to Cart button functionality
                const addToCartButton = document.getElementById("popupAddToCartButton");
                addToCartButton.onclick = function() {
                    addToCart(product.product_index_no);
                };

                // Show the popup
                popupOverlay.style.display = "block";
                productPopup.style.display = "block";
                
                // Prevent body scrolling
                document.body.style.overflow = 'hidden';
            }

            window.closePopup = function() {
                popupOverlay.style.display = "none";
                productPopup.style.display = "none";
                
                // Re-enable body scrolling
                document.body.style.overflow = '';
            }

            // Close popup when clicking outside
            popupOverlay.addEventListener('click', function(event) {
                if (event.target === popupOverlay) {
                    closePopup();
                }
            });

            // Render products with popup and Add to Cart functionality
            function renderProducts(products) {
                const grid = document.getElementById("product-grid");
                grid.innerHTML = ''; // Clear any existing content

                if (products.length === 0) {
                    const noProducts = document.createElement('div');
                    noProducts.className = 'text-center';
                    noProducts.style.gridColumn = '1 / -1';
                    noProducts.innerHTML = `
                        <h3>No products found</h3>
                        <p>Try adjusting your search criteria or check back later for new products.</p>
                    `;
                    grid.appendChild(noProducts);
                    return;
                }

                products.forEach(product => {
                    const productCard = document.createElement('div');
                    productCard.className = 'product-card';
                    
                    // Create sustainability score badge
                    const sustainabilityScore = document.createElement('div');
                    sustainabilityScore.className = 'sustainability-score';
                    sustainabilityScore.textContent = `Eco Score: ${product.sustainability_score}/10`;
                    
                    // Create image container and image
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'product-image-container';
                    
                    const image = document.createElement('img');
                    image.className = 'product-image';
                    image.src = product.image;
                    image.alt = product.name;
                    
                    imageContainer.appendChild(image);
                    imageContainer.appendChild(sustainabilityScore);
                    
                    // Create content container
                    const content = document.createElement('div');
                    content.className = 'product-content';
                    
                    // Create title
                    const title = document.createElement('h3');
                    title.className = 'product-title';
                    title.textContent = product.name;
                    
                    // Create rating
                    const rating = document.createElement('div');
                    rating.className = 'product-rating';
                    for (let i = 0; i < 5; i++) {
                        const star = document.createElement('span');
                        star.className = 'star';
                        star.textContent = i < Math.round(product.rating) ? '★' : '☆';
                        rating.appendChild(star);
                    }
                    
                    // Create price
                    const price = document.createElement('div');
                    price.className = 'product-price';
                    price.textContent = `₹${product.price}`;
                    
                    // Create actions container
                    const actions = document.createElement('div');
                    actions.className = 'product-actions';
                    
                    // Create view details button
                    const viewButton = document.createElement('button');
                    viewButton.className = 'btn btn-outline';
                    viewButton.textContent = 'View Details';
                    viewButton.onclick = function() {
                        openPopup(product);
                    };
                    
                    // Create add to cart button
                    const addButton = document.createElement('button');
                    addButton.className = 'btn btn-primary';
                    addButton.textContent = 'Add to Cart';
                    addButton.onclick = function(event) {
                        event.stopPropagation();
                        addToCart(product.product_index_no);
                    };
                    
                    // Append all elements
                    actions.appendChild(viewButton);
                    actions.appendChild(addButton);
                    
                    content.appendChild(title);
                    content.appendChild(rating);
                    content.appendChild(price);
                    content.appendChild(actions);
                    
                    productCard.appendChild(imageContainer);
                    productCard.appendChild(content);
                    
                    // Make the entire card clickable for details
                    productCard.addEventListener('click', function() {
                        openPopup(product);
                    });
                    
                    grid.appendChild(productCard);
                });
            }

            // Voice search functionality
            const voiceSearchButton = document.getElementById("voice-search-button");
            const searchInput = document.querySelector(".search-input");

            voiceSearchButton.addEventListener("click", () => {
                // Check if the browser supports SpeechRecognition
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                if (!SpeechRecognition) {
                    showNotification("Voice search is not supported in this browser. Please use Chrome or Edge.", "error");
                    return;
                }

                const recognition = new SpeechRecognition();
                recognition.lang = "en-US";
                recognition.interimResults = false;

                recognition.onstart = () => {
                    voiceSearchButton.textContent = "🎙️";
                    voiceSearchButton.classList.add("listening");
                    showNotification("Voice search activated. Please speak your query.", "success");
                };

                recognition.onresult = (event) => {
                    const query = event.results[0][0].transcript;
                    searchInput.value = query;
                    handleSearch(); // Call the search function with the recognized query
                    voiceSearchButton.textContent = "🎤";
                    voiceSearchButton.classList.remove("listening");
                };

                recognition.onerror = (event) => {
                    showNotification("Voice search failed. Please try again.", "error");
                    console.error("Voice search error:", event.error);
                    voiceSearchButton.textContent = "🎤";
                    voiceSearchButton.classList.remove("listening");
                };

                recognition.onend = () => {
                    voiceSearchButton.textContent = "🎤";
                    voiceSearchButton.classList.remove("listening");
                };

                recognition.start();
            });

            let products = []; // Global array to store products

            // Fetch products from backend
            fetch('getProducts.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    products = data; // Store fetched products globally
                    renderProducts(products); // Render products on page load
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    showNotification('Failed to load products. Please refresh the page.', 'error');
                });

            // Function to handle search dynamically
            function handleSearch() {
                const searchQuery = document.querySelector('.search-input').value.toLowerCase();
                const filteredProducts = products.filter(product =>
                    product.name.toLowerCase().includes(searchQuery) ||
                    (product.DESCRIPTION && product.DESCRIPTION.toLowerCase().includes(searchQuery))
                );
                renderProducts(filteredProducts);
            }

            // Add event listener to the search input
            document.querySelector('.search-input').addEventListener('input', handleSearch);

            // Clear button functionality
            document.getElementById("clear-button").addEventListener("click", () => {
                document.querySelector('.search-input').value = '';
                renderProducts(products);
            });

            // Update cart button on page load
            updateCartButton();
            
            // Add keyboard shortcut for search
            document.addEventListener('keydown', function(event) {
                // Ctrl+K or Cmd+K to focus search
                if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                    event.preventDefault();
                    searchInput.focus();
                }
                
                // Escape key to close popup
                if (event.key === 'Escape' && popupOverlay.style.display === 'block') {
                    closePopup();
                }
            });
        });
    </script>
</body>
</html>
