<?php
require_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$errorMessage = '';
$formData = [
    'username' => '',
    'email' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $errorMessage = 'All fields (Username, Email, and Password) are required.';
        $formData['username'] = $username;
        $formData['email'] = $email;
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'User account created successfully.';
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Error creating user: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">New User</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">User Directory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add New</li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all me-2">
                <i class="bi bi-x-lg me-2"></i>
                Discard
            </a>
            <button type="submit" form="addUserForm" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-person-plus-fill me-2"></i>
                Create Account
            </button>
        </div>
    </div>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0">Profile Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="add.php" method="POST" id="addUserForm" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold text-muted small text-uppercase ls-1">Username</label>
                            <input type="text" class="form-control form-control-lg border-light bg-light rounded-3" id="username" name="username" value="<?php echo htmlspecialchars($formData['username']); ?>" placeholder="e.g. johndoe" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-muted small text-uppercase ls-1">Email Address</label>
                            <input type="email" class="form-control border-light bg-light rounded-3" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" placeholder="john@example.com" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold text-muted small text-uppercase ls-1">Initial Password</label>
                            <input type="password" class="form-control border-light bg-light rounded-3" id="password" name="password" required>
                            <div class="form-text mt-2 text-muted italic">The user can change this password after their first login.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .ls-1 { letter-spacing: 0.1rem; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); opacity: 0.9; }
</style>

