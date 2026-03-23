<?php
require_once '../includes/db.php';

// Get all users
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
$users = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Check for success message
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">User Directory</h1>
            <p class="text-muted">Manage shop administrators and registered customers.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add.php" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-person-plus me-2"></i>
                Add New User
            </a>
        </div>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $successMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- User Table Card -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">All Registered Users</h5>
            <div class="search-box position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="userSearch" class="form-control form-control-sm ps-5 rounded-pill border-light bg-light" placeholder="Search by name or email..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="userTable">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3">User Profile</th>
                            <th class="py-3">Email Address</th>
                            <th class="py-3 text-center">Role</th>
                            <th class="py-3 text-center">Joined Date</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): 
                                $is_admin = ($user['id'] == 0);
                            ?>
                                <tr class="user-row">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=<?php echo $is_admin ? '4e73df' : 'e74a3b'; ?>&color=fff&bold=true" 
                                                     class="rounded-circle" width="40" height="40" alt="User">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($user['username']); ?></h6>
                                                <span class="text-muted smaller">ID: #<?php echo $user['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark small"><?php echo htmlspecialchars($user['email']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($is_admin): ?>
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary px-3">Administrator</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-light text-muted border px-3">Customer</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-icon btn-light border ripple">
                                                <i class="bi bi-pencil-fill text-primary"></i>
                                            </a>
                                            <?php if (!$is_admin): ?>
                                                <button onclick="confirmUserDelete(<?php echo $user['id']; ?>)" class="btn btn-sm btn-icon btn-light border ripple">
                                                    <i class="bi bi-trash-fill text-danger"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No users found in the system.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .smaller { font-size: 0.75rem; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
    }
    
    .user-row { transition: background 0.2s; }
    .user-row:hover { background-color: #fcfdfe !important; }
    
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); opacity: 0.9; }
</style>

<script>
function confirmUserDelete(id) {
    if(confirm('Are you sure you want to delete this user account? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}

document.getElementById('userSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#userTable .user-row');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>

