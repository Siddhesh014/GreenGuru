<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);

if (!$isAdminSession) {
    header("Location: ../../Login page/admin-login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenGuru Admin | Premium Management Suite</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- UI Frameworks -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Custom Style Placeholder (will be added to each page or a global file) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="font-family: 'Inter', sans-serif; background-color: #f8f9fa;">
    <div class="container-fluid">
        <div class="row">

