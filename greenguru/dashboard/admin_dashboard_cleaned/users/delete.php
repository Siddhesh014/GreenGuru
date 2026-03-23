<?php
require_once '../includes/db.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'User ID is required for deletion.';
    header('Location: index.php');
    exit;
}

$userId = intval($_GET['id']);

// Protection: Prevent deleting admin (ID 0)
if ($userId === 0) {
    $_SESSION['error_message'] = 'Primary administrator account cannot be deleted.';
    header('Location: index.php');
    exit;
}

// Delete user from database
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'User account has been permanently removed.';
} else {
    $_SESSION['error_message'] = 'Error deleting user: ' . $conn->error;
}

$stmt->close();

// Redirect back to users page
header('Location: index.php');
exit;
?>

