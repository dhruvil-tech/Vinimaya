<?php 
include VIEW_PATH . '/layouts/navigation.php'; 
include VIEW_PATH . '/layouts/header.php'; 
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Expense Reports & Analytics</h4>
                        <div class="btn-group">
                            <a href="index.php?controller=dashboard&action=index" 
                               class="btn btn-outline-light btn-sm">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                            <a href="index.php?controller=reports&action=export&format=pdf&month=<?php echo $month; ?>&year=<?php echo $year; ?>" 
                               class="btn btn-light btn-sm" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </a>
                            <a href="index.php?controller=reports&action=export&format=excel&month=<?php echo $month; ?>&year=<?php echo $year; ?>" 
                               class="btn btn-light btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" action="" class="row g-3 align-items-end">
                        <input type="hidden" name="controller" value="reports">
                        <input type="hidden" name="action" value="index">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Month</label>
                            <select name="month" class="form-select" onchange="this.form.submit()">
                                <?php foreach(Helpers::getMonthsList() as $num => $name): ?>
                                    <option value="<?php echo $num; ?>" 
                                        <?php echo ($month == $num) ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Year</label>
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                <?php foreach(Helpers::getYearsList() as $yr): ?>
                                    <option value="<?php echo $yr; ?>" 
                                        <?php echo ($year == $yr) ? 'selected' : ''; ?>>
                                        <?php echo $yr; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-filter me-1"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="card border-0 bg-white shadow-sm">
                                <div class="card-body">
                                    <h3 class="text-danger">₹<?php echo number_format($total_spent, 2); ?></h3>
                                    <p class="text-muted mb-0">Total Spent</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card border-0 bg-white shadow-sm">
                                <div class="card-body">
                                    <h3 class="text-info"><?php echo $category_stats->rowCount(); ?></h3>
                                    <p class="text-muted mb-0">Categories Used</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card border-0 bg-white shadow-sm">
                                <div class="card-body">
                                    <h3 class="text-success">₹<?php echo number_format($total_spent / date('t'), 2); ?></h3>
                                    <p class="text-muted mb-0">Daily Average</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="card border-0 bg-white shadow-sm">
                                <div class="card-body">
                                    <h3 class="text-warning"><?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></h3>
                                    <p class="text-muted mb-0">Period</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Spending by Category</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="categoryChart" width="400" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Spending Trend</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="trendChart" width="400" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Category-wise Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                    <th>Percentage</th>
                                                    <th>Transactions</th>
                                                    <th>Average</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $category_stats->execute();
                                                while($category = $category_stats->fetch()): 
                                                    $percentage = Helpers::calculatePercentage($category['spent'], $total_spent);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="me-2"><?php echo $category['icon']; ?></span>
                                                        <?php echo $category['name']; ?>
                                                    </td>
                                                    <td class="fw-bold text-danger">₹<?php echo number_format($category['spent'], 2); ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?php echo Helpers::getProgressColor($percentage); ?>" 
                                                                 role="progressbar" 
                                                                 style="width: <?php echo $percentage; ?>%"
                                                                 aria-valuenow="<?php echo $percentage; ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                <?php echo $percentage; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $category['transaction_count']; ?></td>
                                                    <td class="text-muted">
                                                        ₹<?php echo $category['transaction_count'] > 0 ? number_format($category['spent'] / $category['transaction_count'], 2) : '0.00'; ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const chartData = {
        categories: <?php echo json_encode($chart_data['categories']); ?>,
        amounts: <?php echo json_encode($chart_data['amounts']); ?>,
        colors: <?php echo json_encode($chart_data['colors']); ?>,
        trendMonths: <?php echo json_encode($chart_data['trend_months']); ?>,
        trendAmounts: <?php echo json_encode($chart_data['trend_amounts']); ?>
    };
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
