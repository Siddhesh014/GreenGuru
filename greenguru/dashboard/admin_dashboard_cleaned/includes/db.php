<?php
session_start();

// Admin Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 0) {
    header("Location: ../../Login page/login.html");
    exit();
}

// Database connection
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'project';
$port = getenv('DB_PORT') ?: "3307";

// Create connection
$conn = new mysqli($host, $user, $password, $database,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function getOrderCount() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) AS count FROM orders");
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get user count
function getUserCount() {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get product count
function getProductCount() {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM products";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get cart item count
function getCartItemCount() {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM cart";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Function to get recent products
function getRecentProducts() {
    global $conn;
    $sql = "SELECT * FROM products ORDER BY product_index_no DESC LIMIT 5";
    $result = $conn->query($sql);
    $products = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}
?>

