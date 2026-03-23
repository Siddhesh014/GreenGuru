<?php
require_once 'includes/db.php';

// Get counts from database
$userCount = getUserCount();
$productCount = getProductCount();
$orderCount = getOrderCount();
$recentProducts = getRecentProducts(5);

// Calculate Total Revenue and Profit
$totalRevenue = 0;
$totalProfit = 0;
$revenueQuery = $conn->query("SELECT total, subtotal FROM orders");
while ($row = $revenueQuery->fetch_assoc()) {
    $totalRevenue += $row['total'];
}

// Calculate profit: This is a simplified version. Real profit would subtract cost_price * quantity.
// For now, let's assume a 30% margin if cost_price isn't perfectly tracked for all history.
$totalProfit = $totalRevenue * 0.3; 

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <!-- Dashboard Header -->
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">Commerce Overview</h1>
            <p class="text-muted">Welcome back, Administrator. Here's your shop status today.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">Export PDF</button>
            </div>
            <button type="button" class="btn btn-sm btn-primary d-flex align-items-center">
                <i class="bi bi-calendar3 me-2"></i>
                This Month
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon-wrapper bg-primary-subtle text-primary rounded-circle p-3">
                            <i class="bi bi-currency-rupee fs-4"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success">+12.5%</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small ls-1">Total Revenue</h6>
                    <h3 class="mb-0 fw-bold">₹<?php echo number_format($totalRevenue, 2); ?></h3>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon-wrapper bg-success-subtle text-success rounded-circle p-3">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success">+8.2%</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small ls-1">Est. Profit</h6>
                    <h3 class="mb-0 fw-bold text-success">₹<?php echo number_format($totalProfit, 2); ?></h3>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-success" style="width: 60%"></div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon-wrapper bg-warning-subtle text-warning rounded-circle p-3">
                            <i class="bi bi-cart-check fs-4"></i>
                        </div>
                        <span class="badge bg-danger-subtle text-danger">-2.4%</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small ls-1">Total Orders</h6>
                    <h3 class="mb-0 fw-bold"><?php echo number_format($orderCount); ?></h3>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon-wrapper bg-info-subtle text-info rounded-circle p-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success">+15%</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small ls-1">New Customers</h6>
                    <h3 class="mb-0 fw-bold"><?php echo number_format($userCount); ?></h3>
                </div>
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-info" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row mb-5">
        <!-- Sales Performance Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Sales Performance</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">Last 30 Days</button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Recent Products</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (count($recentProducts) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentProducts as $product): ?>
                                <div class="list-group-item border-0 px-0 mb-3 d-flex align-items-center">
                                    <div class="rounded-3 bg-light p-1 me-3">
                                        <img src="../../Product Page/<?php echo htmlspecialchars($product['image'] ?? 'assets/default-product.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <div class="d-flex align-items-center small text-muted">
                                            <span>₹<?php echo number_format($product['price'], 2); ?></span>
                                            <span class="mx-1">•</span>
                                            <span class="text-success"><?php echo $product['stock']; ?> in stock</span>
                                        </div>
                                    </div>
                                    <a href="products/edit.php?id=<?php echo $product['product_index_no']; ?>" class="btn btn-sm btn-light border rounded-pill px-3">Edit</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="products/index.php" class="btn btn-primary w-100 rounded-pill mt-2">View All Inventory</a>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
                            <p class="text-muted">No products listed yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Custom Styles for Premium Dashboard -->
<style>
    .ls-1 { letter-spacing: 0.1rem; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    
    .card { transition: all 0.2s ease-in-out; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important; }
    
    .main-content { background-color: #f8f9fa; min-height: 100vh; }
    .dashboard-header { background-color: transparent; }
    
    .stats-icon-wrapper {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
    $salesQuery = $conn->query("
        SELECT 
            DATE(order_date) as date,
            SUM(total) as daily_sales,
            COUNT(order_id) as order_count
        FROM orders
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(order_date)
        ORDER BY date ASC
    ");
    $dates = [];
    $salesData = [];
    while ($row = $salesQuery->fetch_assoc()) {
        $dates[] = date('M j', strtotime($row['date']));
        $salesData[] = $row['daily_sales'];
    }
    
    // Fill in mock data if database is empty for demo purposes
    if (empty($dates)) {
        $dates = ['Mar 18', 'Mar 19', 'Mar 20', 'Mar 21', 'Mar 22'];
        $salesData = [540, 890, 1200, 300, 750];
    }
    ?>

    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Revenue (₹)',
                data: <?php echo json_encode($salesData); ?>,
                borderColor: '#0d6efd',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: gradient,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: { 
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: {
                        callback: function(value) { return '₹' + value; }
                    }
                }
            }
        }
    });
});
</script>


</script>
     
