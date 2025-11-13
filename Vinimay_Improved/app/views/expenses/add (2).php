<?php include VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Expense</h4>
                        <div class="btn-group">
                            <a href="index.php?controller=dashboard&action=index" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                            <a href="index.php?controller=expense&action=list" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="" id="expenseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="amount" name="amount" 
                                           inputmode="numeric" required 
                                           placeholder="0" value="<?php echo $_POST['amount'] ?? ''; ?>">
                                </div>
                                <div class="form-text">Enter the amount spent</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expense_date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" 
                                       required value="<?php echo $_POST['expense_date'] ?? date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php while($cat = $categories->fetch()): ?>
                                    <option value="<?php echo $cat['name']; ?>" 
                                        <?php echo (($_POST['category'] ?? '') == $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Add a brief description (optional)"><?php echo $_POST['description'] ?? ''; ?></textarea>
                            <div class="form-text">Where did you spend this money? e.g., "Groceries from Big Bazaar"</div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <!-- <div class="mb-4">
                            <label class="form-label fw-bold">Quick Amounts (₹)</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="50">50</button>
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="100">100</button>
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="200">200</button>
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="500">500</button>
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="1000">1000</button>
                                <button type="button" class="btn btn-outline-primary quick-amount" data-amount="2000">2000</button>
                            </div>
                        </div> -->

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i>Save Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Expenses Preview -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Today's Expenses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $today_expenses = $this->expense->read($_SESSION['user_id'], [
                        'start_date' => date('Y-m-d'),
                        'end_date' => date('Y-m-d')
                    ]);
                    ?>
                    
                    <?php if($today_expenses->rowCount() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($expense = $today_expenses->fetch()): ?>
                                    <tr>
                                        <td><?php echo date('h:i A', strtotime($expense['created_at'])); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $expense['category']; ?></span></td>
                                        <td class="text-truncate" style="max-width: 200px;"><?php echo $expense['description'] ?: '-'; ?></td>
                                        <td class="fw-bold text-danger"><?php echo number_format($expense['amount'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No expenses recorded today.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
