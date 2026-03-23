<?php
require_once '../includes/db.php';

// Get total orders and total revenue for header stats
$statsResult = $conn->query("SELECT COUNT(*) as count, SUM(total) as revenue FROM orders");
$stats = $statsResult->fetch_assoc();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <div class="dashboard-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-0">Order Tracking</h1>
            <p class="text-muted">Monitor sales, shipping statuses, and customer fulfillment.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary d-flex align-items-center rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>
                Print Report
            </button>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon-wrapper bg-primary-subtle text-primary rounded-3 p-3 me-3">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Total Orders</h6>
                        <h4 class="mb-0 fw-bold"><?php echo number_format($stats['count']); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="stats-icon-wrapper bg-success-subtle text-success rounded-3 p-3 me-3">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small text-uppercase fw-bold">Revenue Flow</h6>
                        <h4 class="mb-0 fw-bold">₹<?php echo number_format($stats['revenue'], 2); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card border-0 shadow-sm overflow-hidden mb-5">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0">Fulfillment Queue</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Order ID</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Shipping Address</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Items</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
                        while ($order = $orders->fetch_assoc()):
                            $status_class = '';
                            switch($order['status']) {
                                case 'Delivered': $status_class = 'bg-success'; break;
                                case 'Shipped': $status_class = 'bg-info'; break;
                                case 'Processing': $status_class = 'bg-warning'; break;
                                default: $status_class = 'bg-secondary';
                            }
                        ?>
                        <tr class="order-row border-bottom">
                            <td class="ps-4">
                                <span class="fw-bold text-dark">#ORD-<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <div class="smaller text-muted"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></div>
                            </td>
                            <td>
                                <div class="fw-bold small text-dark"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                <div class="smaller text-muted"><?php echo htmlspecialchars($order['email']); ?></div>
                            </td>
                            <td>
                                <div class="smaller text-dark text-truncate" style="max-width: 200px;" title="<?php echo $order['address'] . ', ' . $order['city']; ?>">
                                    <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?>
                                </div>
                            </td>
                            <td class="fw-bold text-dark">
                                ₹<?php echo number_format($order['total'], 2); ?>
                            </td>
                            <td>
                                <div class="avatar-group d-flex">
                                    <?php
                                    $order_id = $order['order_id'];
                                    $products = $conn->query("
                                        SELECT p.image, oi.quantity
                                        FROM order_items oi
                                        JOIN products p ON p.product_index_no = oi.product_index_no
                                        WHERE oi.order_id = $order_id
                                    ");
                                    $count = 0;
                                    while ($item = $products->fetch_assoc()):
                                        if($count < 3):
                                    ?>
                                        <div class="rounded-circle border bg-white overflow-hidden" 
                                             style="width: 32px; height: 32px; margin-left: <?php echo $count > 0 ? '-10px' : '0'; ?>; z-index: <?php echo 10 - $count; ?>;">
                                            <img src="../../../Product Page/<?php echo $item['image']; ?>" 
                                                 title="<?php echo $item['quantity']; ?>x Item"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php 
                                        endif;
                                        $count++; 
                                    endwhile; 
                                    if($count > 3): ?>
                                        <div class="rounded-circle border bg-light d-flex align-items-center justify-content-center smaller text-muted" 
                                             style="width: 32px; height: 32px; margin-left: -10px; z-index: 1;">
                                             +<?php echo ($count - 3); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo $status_class; ?> px-3"><?php echo $order['status']; ?></span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="update_status.php" method="POST" class="d-inline-flex gap-2 align-items-center">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status" class="form-select form-select-sm border-light bg-light rounded-pill px-3 shadow-none" style="width: auto;" onchange="this.form.submit()">
                                        <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
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
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    
    .order-row:hover { background-color: #fcfdfe !important; }
    
    .stats-icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-select:focus { border-color: #0d6efd; box-shadow: none; }
    
    @media print {
        .dashboard-header, .sidebar, .btn-toolbar, td form { display: none !important; }
        .main-content { width: 100% !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
