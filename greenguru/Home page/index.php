<?php
session_start();
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);
if ($isAdminSession) {
    header("Location: ../dashboard/admin_dashboard_cleaned/index.php");
    exit();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenGURU - Eco-Friendly Products for a Sustainable Future</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
.user-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: inherit;
}
</style>
</head>

<body>
    <header class="bg-primary text-primary-foreground sticky top-0 z-50">
        <div class="container">
            <nav>
                <a href="/" class="logo">
                    <i data-lucide="leaf"></i>
                    GreenGURU
                </a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="../Product Page/product-interface.php" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>Products</a>
                    <a href="../About page/about.html" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>About</a>
                    <?php if ($isAdminSession): ?>
                        <a href="../dashboard/admin_dashboard_cleaned/index.php" class="btn btn-outline" style="margin-right: 10px; border-color: #fff; color: #fff;">Dashboard</a>
                    <?php endif; ?>
                    <?php if ($user_id): ?>
                        <!-- <a href="../user-profile.php" class="user-circle" id="userCircle">
                            <i data-lucide="user"></i>
                        </a> -->
                        <div class="user-circle">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="../user-profile.php" class="user-link">
                                    <i data-lucide="user"></i>
                                </a>
                            <?php else: ?>
                                <a href="../Login page/login.html" class="user-link">
                                    <i data-lucide="user"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <a href="../Login page/login.html" class="btn btn-secondary">Login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Welcome to GreenGURU</h1>
                    <p>Your one-stop shop for eco-friendly products. Join us in making the world a better place, one sustainable choice at a time.</p>
                    <div class="hero-buttons">
                        <a href="../Product Page/product-interface.html" class="btn btn-secondary btn-lg" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>Shop Now</a>
                        <a href="../About page/about.html" class="btn btn-outline btn-lg" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>Learn More</a>
                    </div>
                </div>
                <div class="eco-friendly-carousel hero-image">
                    <div class="carousel-track">
                        <img src="./scroll-img/scroll.png.jpg" alt="Eco-Friendly Product 1">
                        <img src="./scroll-img/scroll2.png" alt="Eco-Friendly Product 2">
                        <img src="./scroll-img/scroll1.png.jpg" alt="Eco-Friendly Product 3">
                        <!-- Repeat images for continuous loop -->
                        <img src="./scroll-img/scroll.png.jpg" alt="Image 1">
                        <img src="./scroll-img/scroll2.png" alt="Image 2">
                        <img src="./scroll-img/scroll1.png.jpg" alt="Image 3">
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="trusted-brands">
            <h2>TRUSTED BY LEADING BRANDS</h2>
            <div class="brand-logos">
                <div class="brand-logo">
                    <img src="./trusted/IKEA-Logo.wine.png" alt="IKEA" class="svg_img">
                    <span>IKEA</span>
                </div>
                <div class="brand-logo">
                    <img src="./trusted/nike.jpeg" alt="Nike" class="svg_img">
                    <span>NIKE</span>
                </div>
                <div class="brand-logo">
                    <img src="./trusted/amazon.png" alt="Amazon" class="svg_img">
                    <span>AMAZON</span>
                </div>
                <div class="brand-logo">
                    <img src="./trusted/image.png" alt="Blinkit" class="svg_img">
                    <span>Blinkit</span>
                </div>
            </div>
        </section> -->

        <section class="featured-products">
            <div class="container">
                <h2 class="section-title">Our Featured Products</h2>
                <p class="section-description">Discover our handpicked selection of sustainable products that help reduce environmental impact</p>
                <div class="product-grid" id="featured-products"></div>
            </div>
        </section>

        <section class="eco-businesses">
            <div class="container">
                <h2 class="section-title">Featured Eco-Friendly Businesses</h2>
                <p class="section-description">We partner with businesses that share our commitment to sustainability and ethical practices</p>
                <div class="business-grid" id="eco-businesses"></div>
            </div>
        </section>

        <!-- Blog Articles Section -->
        <section class="blog-articles">
            <div class="container">
                <h2 class="section-title">Sustainability Blog</h2>
                <p class="section-description">Discover tips, insights, and stories about sustainable living</p>
                <div class="article-grid" id="blog-articles"></div>
            </div>
        </section>

        <section class="newsletter">
            <div class="container">
                <h2>Join Our Green Community</h2>
                <p>Stay updated with the latest eco-friendly products and sustainability tips</p>
                <form id="newsletter-form" class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-secondary">Subscribe</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>About GreenGURU</h3>
                    <p>We're on a mission to make sustainable living accessible and enjoyable for everyone. Our carefully curated products help reduce environmental impact while supporting ethical businesses.</p>
                    <div class="footer-logo">
                        <i data-lucide="leaf"></i>
                        <span>GreenGURU</span>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../Product Page/product-interface.php" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>All Products</a></li>
                        <li><a href="../About page/about.html" data-userinfo='<?php echo json_encode(["id" => $user_id, "username" => $username, "email" => $email]); ?>'>About Us</a></li>
                        <li><a href="/blog">Sustainability Blog</a></li>
                        <li><a href="/contact">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="/faq">Frequently Asked Questions</a></li>
                        <li><a href="/shipping">Shipping & Returns</a></li>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p>Follow us on social media for sustainable living tips, product updates, and more.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i data-lucide="facebook"></i> Facebook</a>
                        <a href="#" class="social-link"><i data-lucide="twitter"></i> Twitter</a>
                        <a href="#" class="social-link"><i data-lucide="instagram"></i> Instagram</a>
                        <a href="#" class="social-link"><i data-lucide="linkedin"></i> LinkedIn</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 GreenGURU. All rights reserved. | Made with <span class="heart">❤</span> for the planet</p>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Lucide icons
        lucide.createIcons();

        // Featured products data
        const featuredProducts = [
            { id: 1, name: "Bamboo Toothbrush", price: 5.99, image: "../Product Page/products/product1.jpg", description: "Made of Neem Tree, biodegradable and eco-friendly alternative to plastic toothbrushes." },
            { id: 2, name: "Family Toothbrush Set", price: 15.99, image: "../Product Page/products/product2.jpg", description: "Made of three different sustainable materials, perfect for the whole family." },
            { id: 3, name: "Eco Bag", price: 9.99, image: "../Product Page/products/product4.jpg", description: "Reusable eco-friendly cover for bottles, reduces plastic waste and keeps drinks cold longer." },
        ];

        // Render featured products
        const productGrid = document.getElementById('featured-products');
        featuredProducts.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.innerHTML = `
                <img src="${product.image}" alt="${product.name}" class="product-image">
                <div class="product-content">
                    <h3 class="product-title">${product.name}</h3>
                    <p class="product-description">${product.description}</p>
                </div>
                <div class="product-footer">
                    <span class="product-price">₹${product.price.toFixed(2)}</span>
                    <button class="btn btn-outline">View Details</button>
                </div>
            `;
            productGrid.appendChild(productCard);
        });

        // Business listings data
        const businessListings = [
            { name: "EcoHome Solutions", description: "Sustainable home improvement products that focus on solutions that reduce energy consumption, minimize waste, and encourage eco-friendly living.", rating: 4.5 },
            { name: "GreenBeauty Co.", description: "Organic and cruelty-free beauty brand focused on sustainable and eco-friendly beauty products that are free from harmful chemicals.", rating: 4.8 },
            { name: "EarthWear Apparel", description: "Eco-friendly fashion for all dedicated to creating clothing with minimal environmental impact using organic, recycled, and ethically sourced materials.", rating: 4.2 },
        ];

        // Render business listings
        const businessGrid = document.getElementById('eco-businesses');
        businessListings.forEach(business => {
            const businessCard = document.createElement('div');
            businessCard.className = 'business-card';
            businessCard.innerHTML = `
                <h3 class="business-name">${business.name}</h3>
                <p class="business-description">${business.description}</p>
                <div class="business-rating">
                    <span class="star-icon">★</span>
                    <span>${business.rating.toFixed(1)}</span>
                </div>
            `;
            businessGrid.appendChild(businessCard);
        });

        // Blog articles data
        const blogArticles = [
            { title: "10 Easy Ways to Reduce Your Carbon Footprint", image: "./scroll-img/scroll.png.jpg", category: "Lifestyle" },
            { title: "The Rise of Sustainable Fashion: Trends to Watch", image: "../Product Page/products/product2.jpg", category: "Fashion" },
            { title: "How to Create an Eco-Friendly Home Office", image: "../Product Page/products/product4.jpg", category: "Home" },
        ];

        // Render blog articles
        const articleGrid = document.getElementById('blog-articles');
        if (articleGrid) {
            blogArticles.forEach(article => {
                const articleCard = document.createElement('div');
                articleCard.className = 'article-card';
                articleCard.innerHTML = `
                    <img src="${article.image}" alt="${article.title}" class="article-image">
                    <div class="article-content">
                        <span class="article-category">${article.category}</span>
                        <h3 class="article-title">${article.title}</h3>
                        <a href="#" class="btn btn-outline">Read More</a>
                    </div>
                `;
                articleGrid.appendChild(articleCard);
            });
        }

        // Handle newsletter form submission
        const newsletterForm = document.getElementById('newsletter-form');
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = e.target.querySelector('input[type="email"]').value;
            alert(`Thank you for subscribing with email: ${email}`);
            e.target.reset();
        });
    });
    </script>
    <script defer src="https://app.fastbots.ai/embed.js" data-bot-id="cm7xpcbye002foek5z3hktdz8"></script>
</body>

</html>
