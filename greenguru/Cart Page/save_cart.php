<?php
session_start();
header('Content-Type: application/json');

// Database connection
$conn = new mysqli(
    getenv('DB_HOST') ?: 'localhost',
    getenv('DB_USER') ?: 'root',
    getenv('DB_PASSWORD') ?: '',
    getenv('DB_NAME') ?: 'project',
    getenv('DB_PORT') ?: '3307'
);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Get the incoming data
$user_id = $_SESSION['user_id'] ?? null;
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = $user_id !== null ? (int)$user_id : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);
$product_index_no = $data['product_index_no'];
$quantity = $data['quantity'] ?? 1;

if (!$user_id || $isAdminSession) {
    die(json_encode(['error' => 'User not logged in']));
}

// SQL query to insert or update the cart
$sql = "INSERT INTO cart (user_id, product_index_no, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        quantity = quantity + VALUES(quantity)";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $product_index_no, $quantity);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => 'Product added to cart']);
} else {
    echo json_encode(['error' => 'Failed to add product to cart']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

