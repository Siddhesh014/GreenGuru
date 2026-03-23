<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // Disable error display for JSON responses
// Ensure no output before JSON response
ob_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Order ID is required"]);
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Database connection
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "project";
$port = getenv('DB_PORT') ?: "3307";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Debug: Log the order_id and user_id
error_log("Checking order_id: $order_id for user_id: $user_id");

// First verify that the order belongs to the logged-in user
// Note: Using order_id as the column name based on your database structure
$check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $order_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Order not found or does not belong to this user"]);
    exit();
}

// Get order items with product details
// Adjust the query based on your actual table structure
$stmt = $conn->prepare("
        SELECT oi.*, p.name, p.price, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_index_no = p.product_index_no
        WHERE oi.order_id = ?
    ");



if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database query error: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    // Make sure we have all required fields or provide defaults
    $items[] = [
        'name' => $row['name'] ?? 'Unknown Product',
        'price' => $row['price'] ?? 0,
        'quantity' => $row['quantity'] ?? 1,
        'image' => $row['image'] ?? 'default.jpg',
        // Include any other fields you need
    ];
}

// Clear any output buffered content
ob_end_clean();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($items);

$stmt->close();
$conn->close();
?>
