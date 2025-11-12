<div class="col-lg-3 mb-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Quick Actions</h6>
        </div>
        <div class="card-body p-0">
            <!-- ... quick actions links ... -->
        </div>
    </div>

    <!-- Monthly Summary Widget -->
    <div class="card shadow mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>This Month</h6>
        </div>
        <div class="card-body">
            <div class="text-center">
                <!-- ✅ UPDATED: Use the sidebar_data -->
                <h3 class="text-danger">₹<?php echo number_format($sidebar_data['total_spent'], 2); ?></h3>
                <p class="text-muted mb-0">Total Spent</p>
                <small class="text-muted"><?php echo date('F Y'); ?></small>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="card shadow mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Stats</h6>
        </div>
        <div class="card-body">
            <!-- ✅ UPDATED: Use the sidebar_data -->
            <div class="d-flex justify-content-between mb-2">
                <span>Today:</span>
                <strong class="text-danger">₹<?php 
                    $total_today = 0;
                    while($expense = $sidebar_data['today_expenses']->fetch()) {
                        $total_today += $expense['amount'];
                    }
                    echo number_format($total_today, 2);
                ?></strong>
            </div>
            <!-- ... rest of quick stats ... -->
        </div>
    </div>
</div>