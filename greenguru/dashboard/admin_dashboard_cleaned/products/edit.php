<?php
require_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE product_index_no = $id");
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

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

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, rating=?, sustainability_score=?, image=?, stock=?, DESCRIPTION=? WHERE product_index_no=?");
    $stmt->bind_param("sdiisisi", $name, $price, $rating, $score, $image, $stock, $description, $id);

    if ($stmt->execute()) {
        $success = "Product updated successfully!";
        // Refresh product data
        $result = $conn->query("SELECT * FROM products WHERE product_index_no = $id");
        $product = $result->fetch_assoc();
    } else {
        $error = "Error updating product: " . $conn->error;
    }
}
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">Edit Product</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#<?php echo $id; ?></li>
                </ol>
            </nav>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="index.php" class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all me-2">
                <i class="bi bi-arrow-left me-2"></i>
                Cancel
            </a>
            <button type="submit" form="editProductForm" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-save me-2"></i>
                Save Changes
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0">General Specification</h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" id="editProductForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Product Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" class="form-control form-control-lg border-light bg-light rounded-3" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Description</label>
                            <textarea name="description" class="form-control border-light bg-light rounded-3" rows="4"><?php echo htmlspecialchars($product['DESCRIPTION'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Unit Price (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-light bg-light rounded-start-3">₹</span>
                                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" class="form-control border-light bg-light rounded-end-3" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Stock Level</label>
                                <input type="number" name="stock" value="<?php echo $product['stock'] ?? 0; ?>" class="form-control border-light bg-light rounded-3" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Public Rating (0-5)</label>
                                <input type="number" step="0.1" max="5" name="rating" value="<?php echo $product['rating']; ?>" class="form-control border-light bg-light rounded-3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase ls-1">Sustainability Score (0-10)</label>
                                <input type="number" max="10" name="score" value="<?php echo $product['sustainability_score']; ?>" class="form-control border-light bg-light rounded-3" required>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Image Reference Path</label>
                            <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" class="form-control border-light bg-light rounded-3" required>
                            <small class="text-muted mt-1 d-block italic">Path relative to 'Product Page/' directory.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold mb-0">Live Preview</h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="rounded-4 bg-light p-4 mb-3 border d-inline-block">
                        <img src="../../../Product Page/<?php echo $product['image']; ?>" 
                             alt="Preview" 
                             class="img-fluid rounded-3 shadow-sm" 
                             style="max-height: 200px; object-fit: contain;"
                             onerror="this.src='../../assets/img/placeholder.png'">
                    </div>
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                    <h5 class="text-primary fw-bold mb-3">₹<?php echo number_format($product['price'], 2); ?></h5>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-success-subtle text-success px-3"><?php echo $product['sustainability_score']; ?>/10 Eco Score</span>
                        <span class="badge bg-warning-subtle text-warning px-3"><?php echo $product['rating']; ?> ⭐ Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .ls-1 { letter-spacing: 0.05rem; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); opacity: 0.9; }
</style>
