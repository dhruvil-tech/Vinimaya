<?php include VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Expense</h4>
                        <div class="btn-group">
                            <a href="index.php?controller=dashboard&action=index" class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                            <a href="index.php?controller=expense&action=list" class="btn btn-dark btn-sm">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="amount" name="amount" 
                                           inputmode="numeric" required 
                                           value="<?php echo $this->expense->amount; ?>">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expense_date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" 
                                       required value="<?php echo $this->expense->expense_date; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php while($cat = $categories->fetch()): ?>
                                    <option value="<?php echo $cat['name']; ?>" 
                                        <?php echo ($this->expense->category == $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Add a brief description (optional)"><?php echo $this->expense->description; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=expense&action=list" class="btn btn-outline-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning px-4">
                                Update Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>