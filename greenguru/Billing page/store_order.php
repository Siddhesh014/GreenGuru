<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');
if (ob_get_length()) {
    ob_clean();
}

$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') ?: "";
$dbname = getenv('DB_NAME') ?: "project";
$port = getenv('DB_PORT') ?: "3307";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
    exit();
}

// Order details
$fullName = $data['fullName'] ?? '';
$email = $data['email'] ?? '';
$address = $data['address'] ?? '';
$city = $data['city'] ?? '';
$country = $data['country'] ?? '';
$postalCode = $data['postalCode'] ?? '';
$subtotal = $data['subtotal'] ?? 0;
$tax = $data['tax'] ?? 0;
$total = $data['total'] ?? 0;
$payment_id = $data['payment_id'] ?? '';

if (empty($fullName) || empty($email) || empty($address)) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Step 1: Insert order into `orders` table
$order_sql = $conn->prepare("INSERT INTO orders (user_id, full_name, email, address, city, country, postal_code, subtotal, tax, total, payment_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$order_sql->bind_param("issssssddds", $user_id, $fullName, $email, $address, $city, $country, $postalCode, $subtotal, $tax, $total, $payment_id);

if ($order_sql->execute()) {
    $order_id = $conn->insert_id; // Get the newly inserted order ID

    // Step 2: Fetch items from cart
    $cart_query = $conn->prepare("SELECT product_index_no, quantity FROM cart WHERE user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();

    // Step 3: Insert each cart item into order_items
    $item_sql = $conn->prepare("INSERT INTO order_items (order_id, product_index_no, quantity) VALUES (?, ?, ?)");
    while ($item = $cart_result->fetch_assoc()) {
        $item_sql->bind_param("iii", $order_id, $item['product_index_no'], $item['quantity']);
        $item_sql->execute();
    }
    $item_sql->close();
    $cart_query->close();

    // Step 4: Clear the user's cart
    $delete_sql = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $delete_sql->bind_param("i", $user_id);
    $delete_sql->execute();
    $delete_sql->close();

    echo json_encode(["success" => true, "message" => "Order placed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error placing order: " . $order_sql->error]);
}

$order_sql->close();
$conn->close();
?>
