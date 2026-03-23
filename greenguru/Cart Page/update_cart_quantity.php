<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$database = getenv('DB_NAME') ?: "project";
$port = getenv('DB_PORT') ?: "3307";

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($_SESSION['user_id']) || !isset($data['product_index_no']) || !isset($data['quantity'])) {
    die(json_encode(['error' => 'Missing required parameters']));
}

$user_id = $_SESSION['user_id'];
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = (int)$user_id;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);

if ($isAdminSession) {
    die(json_encode(['error' => 'Only customer accounts can update cart']));
}

$product_index_no = intval($data['product_index_no']);
$quantity = intval($data['quantity']);

// Ensure quantity is positive
if ($quantity <= 0) {
    die(json_encode(['error' => 'Quantity must be greater than zero']));
}

// SQL to update the quantity
$sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_index_no = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

$stmt->bind_param("iii", $quantity, $user_id, $product_index_no);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated successfully']);
    } else {
        echo json_encode(['error' => 'No matching cart item found']);
    }
} else {
    echo json_encode(['error' => 'Failed to update quantity: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

