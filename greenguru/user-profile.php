<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login page/login.html");
    exit();
}
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = (int)$_SESSION['user_id'];
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);
if ($isAdminSession) {
    header("Location: dashboard/admin_dashboard_cleaned/index.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Database connection
$servername = getenv('DB_HOST') ?: "localhost";
$db_username = getenv('DB_USER') ?: "root";
$db_password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "project";
$port = getenv('DB_PORT') ?: "3307";

$conn = new mysqli($servername, $db_username, $db_password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];

// Initialize order counter for sequential numbering
$order_counter = 1;

while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}

// Handle form submission for profile update
$update_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    
    // Update user information
    $update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_username, $user_id);
    
    if ($update_stmt->execute()) {
        // Update session variables
        $_SESSION['username'] = $new_username;
        
        // Refresh user data
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        $update_message = "Profile updated successfully!";
    } else {
        $update_message = "Error updating profile: " . $conn->error;
    }
}

// Handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify old password
    $check_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $user_data = $check_result->fetch_assoc();
    
    if (password_verify($old_password, $user_data['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_pass_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_pass_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_pass_stmt->execute()) {
                $update_message = "Password updated successfully!";
            } else {
                $update_message = "Error updating password: " . $conn->error;
            }
        } else {
            $update_message = "New passwords do not match!";
        }
    } else {
        $update_message = "Current password is incorrect!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Home page/index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - GreenGURU</title>
    <link rel="stylesheet" href="user-profile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <header class="bg-primary text-primary-foreground sticky top-0 z-50">
        <div class="container">
            <nav>
                <a href="Home page/index.php" class="logo">
                    <i data-lucide="leaf"></i>
                    GreenGURU
                </a>
                <div class="nav-links">
                    <a href="Home page/index.php">Home</a>
                    <a href="Product Page/product-interface.php">Products</a>
                    <a href="About page/about.html">About</a>
                    <div class="user-circle active">
                        <i data-lucide="user"></i>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="profile-container">
        <div class="container">
            <div class="profile-header">
                <h1>My Profile</h1>
                <a href="?logout=true" class="btn btn-outline logout-btn">
                    <i data-lucide="log-out"></i> Logout
                </a>
            </div>

            <?php if (!empty($update_message)): ?>
                <div class="alert <?php echo strpos($update_message, "Error") !== false || strpos($update_message, "incorrect") !== false || strpos($update_message, "not match") !== false ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo $update_message; ?>
                </div>
            <?php endif; ?>

            <div class="profile-grid">
                <div class="profile-sidebar">
                    <div class="profile-avatar">
                        <i data-lucide="user" class="avatar-icon"></i>
                        <h2><?php echo htmlspecialchars($username); ?></h2>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                    <div class="profile-nav">
                        <button class="nav-item active" data-target="profile-info">
                            <i data-lucide="user"></i> Personal Information
                        </button>
                        <button class="nav-item" data-target="password-section">
                            <i data-lucide="lock"></i> Change Password
                        </button>
                        <button class="nav-item" data-target="order-history">
                            <i data-lucide="shopping-bag"></i> Order History
                        </button>
                    </div>
                </div>

                <div class="profile-content">
                    <div id="profile-info" class="profile-section active">
                        <h2>Personal Information</h2>
                        <form method="POST" action="" class="profile-form">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? $username); ?>" readonly class="readonly-input">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? $email); ?>" readonly class="readonly-input">
                                <!-- <small>Email cannot be changed</small> -->
                            </div>
                            <!-- <button type="submit" name="update_profile" class="btn btn-secondary">Save Changes</button> -->
                        </form>
                    </div>

                    <div id="password-section" class="profile-section">
                        <h2>Change Password</h2>
                        <form method="POST" action="" class="profile-form">
                            <div class="form-group">
                                <label for="old_password">Current Password</label>
                                <input type="password" id="old_password" name="old_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-secondary">Update Password</button>
                        </form>
                    </div>

                    <div id="order-history" class="profile-section">
                        <h2>Order History</h2>
                        <?php if (empty($orders)): ?>
                            <div class="empty-state">
                                <i data-lucide="package" class="empty-icon"></i>
                                <p>You haven't placed any orders yet.</p>
                                <a href="Product Page/product-interface.php" class="btn btn-secondary">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="orders-list">
                                <?php 
                                $order_counter = 1; // Reset counter here to ensure correct numbering
                                foreach ($orders as $order): 
                                ?>
                                    <div class="order-card">
                                        <div class="order-header">
                                            <div>
                                                <h3>Order #<?php echo $order_counter; ?></h3>
                                                <p class="order-date">
                                                    <i data-lucide="calendar"></i> 
                                                    <?php echo isset($order['order_date']) ? date('F j, Y', strtotime($order['order_date'])) : 'N/A'; ?>
                                                </p>
                                            </div>
                                            <div class="order-status <?php echo isset($order['status']) ? strtolower($order['status']) : 'processing'; ?>">
                                                <?php echo isset($order['status']) ? htmlspecialchars($order['status']) : 'Processing'; ?>
                                            </div>
                                        </div>
                                        <div class="order-details">
                                            <p><strong>Total:</strong> ₹<?php echo number_format($order['total'], 2); ?></p>
                                            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['address'] . ', ' . $order['city'] . ', ' . $order['country'] . ' - ' . $order['postal_code']); ?></p>
                                            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($order['payment_id'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="order-actions">
                                            <button class="btn btn-outline btn-sm view-details-btn" data-order-id="<?php echo $order['order_id']; ?>">View Details</button>
                                        </div>
                                        
                                        <!-- Order Details Modal (Hidden by default) -->
                                        <div id="order-details-<?php echo $order['order_id']; ?>" class="order-details-modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3>Order #<?php echo $order_counter; ?> Details</h3>
                                                    <button class="close-modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="order-items-loading">Loading order details...</div>
                                                    <div class="order-items-container" style="display: none;">
                                                        <table class="order-items-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Price</th>
                                                                    <th>Quantity</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="order-items-list">
                                                                <!-- Order items will be loaded here via AJAX -->
                                                            </tbody>
                                                        </table>
                                                        <div class="order-summary">
                                                            <div class="summary-row">
                                                                <span>Subtotal</span>
                                                                <span class="subtotal">₹<?php echo number_format($order['subtotal'], 2); ?></span>
                                                            </div>
                                                            <div class="summary-row">
                                                                <span>Tax</span>
                                                                <span class="tax">₹<?php echo number_format($order['tax'], 2); ?></span>
                                                            </div>
                                                            <div class="summary-row summary-total">
                                                                <span>Total</span>
                                                                <span class="total">₹<?php echo number_format($order['total'], 2); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    $order_counter++; // Increment counter after displaying each order
                                endforeach; 
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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
                        <li><a href="Product Page/product-interface.php">All Products</a></li>
                        <li><a href="About page/about.html">About Us</a></li>
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

            // Tab navigation
            const navItems = document.querySelectorAll('.nav-item');
            const profileSections = document.querySelectorAll('.profile-section');

            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Remove active class from all nav items
                    navItems.forEach(nav => nav.classList.remove('active'));
                    
                    // Add active class to clicked nav item
                    item.classList.add('active');
                    
                    // Hide all profile sections
                    profileSections.forEach(section => section.classList.remove('active'));
                    
                    // Show the target section
                    const targetSection = document.getElementById(item.dataset.target);
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                });
            });

            // Modal functionality
            const viewDetailsBtns = document.querySelectorAll('.view-details-btn');
            const closeModalBtns = document.querySelectorAll('.close-modal');
            
            // Open modal when View Details is clicked
            viewDetailsBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const orderId = btn.dataset.orderId;
                    const modal = document.getElementById(`order-details-${orderId}`);
                    modal.classList.add('active');
                    document.body.classList.add('modal-open');
                    
                    // Load order items via AJAX
                    loadOrderItems(orderId);
                });
            });
            
            // Close modal when X is clicked
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.order-details-modal');
                    modal.classList.remove('active');
                    document.body.classList.remove('modal-open');
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target.classList.contains('order-details-modal')) {
                    e.target.classList.remove('active');
                    document.body.classList.remove('modal-open');
                }
            });
            
            // Function to load order items
            function loadOrderItems(orderId) {
                const loadingElement = document.querySelector(`#order-details-${orderId} .order-items-loading`);
                const containerElement = document.querySelector(`#order-details-${orderId} .order-items-container`);
                const itemsListElement = document.querySelector(`#order-details-${orderId} .order-items-list`);
                
                loadingElement.style.display = 'block';
                containerElement.style.display = 'none';
                
                // Debug info
                console.log(`Loading order items for order ID: ${orderId}`);
                
                // Fetch order items
                fetch(`get_order_items.php?order_id=${orderId}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(text => {
                        // Log the raw response for debugging
                        console.log('Raw response:', text);
                        
                        // Try to parse as JSON, but handle errors gracefully
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error("Failed to parse JSON:", e, text);
                            throw new Error(`Invalid JSON response`);
                        }
                    })
                    .then(data => {
                        console.log('Parsed data:', data);
                        
                        if (data.error) {
                            itemsListElement.innerHTML = `<tr><td colspan="4">Error: ${data.error}</td></tr>`;
                        } else if (data.length === 0) {
                            itemsListElement.innerHTML = '<tr><td colspan="4">No items found for this order</td></tr>';
                        } else {
                            // Render order items
                            itemsListElement.innerHTML = data.map(item => {
                                const price = parseFloat(item.price);
                                const quantity = parseInt(item.quantity);
                                const total = price * quantity;
                                
                                return `
                                    <tr>
                                        <td class="product-cell">
                                            <img src="Product Page/${item.image}" alt="${item.name}" class="product-thumbnail">
                                            <span>${item.name}</span>
                                        </td>
                                        <td>₹${price.toFixed(2)}</td>
                                        <td>${quantity}</td>
                                        <td>₹${total.toFixed(2)}</td>
                                    </tr>
                                `;
                            }).join('');
                        }
                        
                        loadingElement.style.display = 'none';
                        containerElement.style.display = 'block';
                    })
                    .catch(error => {
                        console.error("Error loading order items:", error);
                        itemsListElement.innerHTML = `<tr><td colspan="4">Error loading order items: ${error.message}</td></tr>`;
                        loadingElement.style.display = 'none';
                        containerElement.style.display = 'block';
                    });
            }
        });
    </script>
</body>
</html>
