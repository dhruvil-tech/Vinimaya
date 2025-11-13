<?php 
// ✅ Include navigation ONLY ONCE at the top
include VIEW_PATH . '/layouts/navigation.php'; 
include VIEW_PATH . '/layouts/header.php'; 
?>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        This Month's Spending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₹<?php echo number_format($total_spent, 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="text-gray-300">📅</div>  
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Daily Average</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₹<?php echo number_format($total_spent / date('t'), 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Categories Used</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count($chart_data['categories'] ?? []); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Family Size</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $_SESSION['family_size']; ?> People
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center py-4">
                            <h3 class="text-primary">Welcome back, <?php echo $_SESSION['user_name']; ?>! 🎉</h3>
                            <p class="text-muted mb-0">Ready to track your expenses today?</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <?php if(!empty($chart_data['categories'])): ?>
            <div class="row mb-4">
                <!-- Pie Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Spending by Category</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="categoryChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trend Chart -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Spending Trend</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="trendChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Data to Display</h4>
                            <p class="text-muted">Start adding expenses to see beautiful charts and insights!</p>
                            <a href="index.php?controller=expense&action=create" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Your First Expense
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Expenses -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Expenses</h6>
                            <a href="index.php?controller=expense&action=create" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if($recent_expenses->rowCount() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($expense = $recent_expenses->fetch()): ?>
                                            <tr>
                                                <td><?php echo date('M j', strtotime($expense['expense_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark"><?php echo $expense['category']; ?></span>
                                                </td>
                                                <td class="text-truncate" style="max-width: 150px;">
                                                    <?php echo $expense['description'] ?: 'No description'; ?>
                                                </td>
                                                <td class="fw-bold text-danger">₹<?php echo number_format($expense['amount'], 2); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No expenses recorded yet.</p>
                                    <a href="index.php?controller=expense&action=create" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Your First Expense
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <?php include VIEW_PATH . '/layouts/sidebar.php'; ?>
    </div>
</div>

<script>
    <?php if(isset($chart_data) && !empty($chart_data['categories'])): ?>
    const chartData = {
        categories: <?php echo json_encode($chart_data['categories']); ?>,
        amounts: <?php echo json_encode($chart_data['amounts']); ?>,
        colors: <?php echo json_encode($chart_data['colors']); ?>,
        trendMonths: <?php echo json_encode($chart_data['trend_months']); ?>,
        trendAmounts: <?php echo json_encode($chart_data['trend_amounts']); ?>
    };
    <?php endif; ?>
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>