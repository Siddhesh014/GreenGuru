<?php
require_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$success = false;
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $score = $_POST['score'];
    $image = $_POST['image'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO products (name, price, rating, sustainability_score, image, stock, DESCRIPTION) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdiisis", $name, $price, $rating, $score, $image, $stock, $description);

    if ($stmt->execute()) {
        $success = "Product added successfully to inventory!";
    } else {
        $error = "Error adding product: " . $conn->error;
    }
}
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">New Product</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add New</li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all me-2">
                <i class="bi bi-x-lg me-2"></i>
                Discard
            </a>
            <button type="submit" form="addProductForm" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-plus-lg me-2"></i>
                Create Product
            </button>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0">Product Details</h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" id="addProductForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Product Title</label>
                            <input type="text" name="name" placeholder="e.g. Organic Cotton Bag" class="form-control form-control-lg border-light bg-light rounded-3" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Product Description</label>
                            <textarea name="description" placeholder="Describe the item's features and eco-friendliness..." class="form-control border-light bg-light rounded-3" rows="4"></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Unit Price (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-light bg-light rounded-start-3">₹</span>
                                    <input type="number" step="0.01" name="price" placeholder="0.00" class="form-control border-light bg-light rounded-end-3" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Initial Stock</label>
                                <input type="number" name="stock" placeholder="0" class="form-control border-light bg-light rounded-3" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Rating (0-5)</label>
                                <input type="number" step="0.1" max="5" name="rating" placeholder="5.0" class="form-control border-light bg-light rounded-3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Sustainability Score (0-10)</label>
                                <input type="number" max="10" name="score" placeholder="10" class="form-control border-light bg-light rounded-3" required>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Image Filename</label>
                            <input type="text" name="image" placeholder="products/new-item.jpg" class="form-control border-light bg-light rounded-3" required>
                            <small class="text-muted mt-1 d-block italic">Upload the image to 'Product Page/products/' and enter the name here.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .ls-1 { letter-spacing: 0.05rem; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); opacity: 0.9; }
</style>
