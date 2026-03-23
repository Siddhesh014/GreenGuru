<?php
require_once '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Get total product count for summary
$totalProductsResult = $conn->query("SELECT COUNT(*) as count FROM products");
$totalProducts = $totalProductsResult->fetch_assoc()['count'];
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">Product Inventory</h1>
            <p class="text-muted">Manage your catalog, prices, and sustainability scores.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add.php" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm transition-all">
                <i class="bi bi-plus-lg me-2"></i>
                Add New Product
            </a>
        </div>
    </div>

    <!-- Inventory Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon-wrapper bg-success-subtle text-success rounded-3 p-3 me-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Active Listings</h6>
                        <h4 class="mb-0 fw-bold"><?php echo $totalProducts; ?> Products</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">All Inventory</h5>
            <div class="search-box position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="productSearch" class="form-control form-control-sm ps-5 rounded-pill border-light bg-light" placeholder="Search products..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="inventoryTable">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Product</th>
                            <th class="py-3">Category / Info</th>
                            <th class="py-3 text-center">Sustainability</th>
                            <th class="py-3 text-end">Price</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM products ORDER BY product_index_no DESC");
                        while ($row = $result->fetch_assoc()):
                            $imagePath = "../../../Product Page/" . ($row['image'] ?? 'assets/default-product.png');
                        ?>
                        <tr class="product-row">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-3 bg-light p-1 me-3 border">
                                        <img src="<?php echo $imagePath; ?>" 
                                             alt="<?php echo $row['name']; ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                             onerror="this.src='../../assets/img/placeholder.png'">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small"><?php echo htmlspecialchars($row['name']); ?></h6>
                                        <span class="text-muted smaller">ID: #<?php echo str_pad($row['product_index_no'], 5, '0', STR_PAD_LEFT); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal"><?php echo $row['rating']; ?> ⭐ Rating</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold text-success mb-1"><?php echo $row['sustainability_score']; ?>%</span>
                                    <div class="progress" style="width: 60px; height: 4px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo $row['sustainability_score']; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                ₹<?php echo number_format($row['price'], 2); ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['stock'] > 10): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3">In Stock</span>
                                <?php elseif($row['stock'] > 0): ?>
                                    <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger px-3">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="edit.php?id=<?php echo $row['product_index_no']; ?>" class="btn btn-sm btn-icon btn-light border ripple">
                                        <i class="bi bi-pencil-fill text-primary"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $row['product_index_no']; ?>)" class="btn btn-sm btn-icon btn-light border ripple">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .smaller { font-size: 0.75rem; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
    }
    
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); opacity: 0.9; }
    
    .product-row { transition: background 0.2s; }
    .product-row:hover { background-color: #fcfdfe !important; }
    
    .stats-icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .ls-1 { letter-spacing: 0.05rem; }
</style>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to remove this product from inventory? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}

document.getElementById('productSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#inventoryTable .product-row');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>
