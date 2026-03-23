<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Order #$order_id status updated to $status.";
    } else {
        $_SESSION['error_message'] = "Failed to update order status.";
    }
}

header("Location: index.php");
exit();
?>
