<?php
require_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$userId = intval($_GET['id']);
$errorMessage = '';
$successMessage = '';
$formData = [];

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$formData = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($email)) {
        $errorMessage = 'Username and email are required fields.';
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $hashedPassword, $userId);
        } else {
            $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $email, $userId);
        }
        
        if ($stmt->execute()) {
            $successMessage = 'User account updated successfully.';
            // Refresh data
            $formData['username'] = $username;
            $formData['email'] = $email;
        } else {
            $errorMessage = 'Error updating user: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">Edit Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">User Directory</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($formData['username']); ?></li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all me-2">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Directory
            </a>
            <button type="submit" form="editUserForm" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-check-lg me-2"></i>
                Apply Changes
            </button>
        </div>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $successMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center">
                    <div class="avatar-sm me-3">
                         <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($formData['username']); ?>&background=4e73df&color=fff&bold=true" 
                              class="rounded-circle" width="32" height="32">
                    </div>
                    <h5 class="fw-bold mb-0">Identity Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="edit.php?id=<?php echo $userId; ?>" method="POST" id="editUserForm" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold text-muted small text-uppercase ls-1">Full Username</label>
                            <input type="text" class="form-control form-control-lg border-light bg-light rounded-3" id="username" name="username" value="<?php echo htmlspecialchars($formData['username']); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-muted small text-uppercase ls-1">Email Address</label>
                            <input type="email" class="form-control border-light bg-light rounded-3" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold text-muted small text-uppercase ls-1">Secure Password</label>
                            <input type="password" class="form-control border-light bg-light rounded-3" id="password" name="password" placeholder="Leave empty to keep existing password">
                            <div class="form-text mt-2 text-muted italic">Only enter a value if you wish to reset this user's password.</div>
                        </div>
                        
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                <span class="small text-muted">User ID #<?php echo $userId; ?> joined on <?php echo date('F d, Y', strtotime($formData['created_at'])); ?>.</span>
                            </div>
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

