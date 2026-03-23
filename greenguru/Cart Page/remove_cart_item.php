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

$user_id = $_SESSION['user_id'] ?? null;
$product_index_no = $data['product_index_no'];

if (!$user_id) {
    die(json_encode(['error' => 'User not logged in']));
}

// SQL to remove the item
$sql = "DELETE FROM cart WHERE user_id = ? AND product_index_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_index_no);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Item removed from cart']);
} else {
    echo json_encode(['error' => 'Failed to remove item from cart']);
}

$stmt->close();
$conn->close();
?>

