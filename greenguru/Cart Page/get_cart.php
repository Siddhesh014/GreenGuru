<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$database = getenv('DB_NAME') ?: "project";
$port = getenv('DB_PORT') ?: "3307";

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = $user_id !== null ? (int)$user_id : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);

if ($user_id && !$isAdminSession) {
    // Query to join cart and products tables
    $sql = "
        SELECT 
            c.product_index_no, 
            c.quantity, 
            p.name, 
            p.price, 
            p.image 
        FROM 
            cart c
        INNER JOIN 
            products p 
        ON 
            c.product_index_no = p.product_index_no
        WHERE 
            c.user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($cartItems);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Cart access is available for customer accounts only"]);
}

$conn->close();
?>

