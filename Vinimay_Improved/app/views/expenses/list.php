<?php include VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-list me-2"></i>All Expenses</h4>
                        <div class="btn-group">
                            <a href="index.php?controller=dashboard&action=index" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                            <a href="index.php?controller=expense&action=create" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i>Add New
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Filters removed -->

                <!-- Summary -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                Total Expenses: <span class="text-danger fw-bold"><?php echo isset($total_spent) ? number_format($total_spent, 2) : '0.00'; ?></span>
                            </h5>
                            <small class="text-muted">
                               <?php 
                                    $expense_count = isset($expenses) && is_array($expenses) ? count($expenses) : 0;
                                    echo $expense_count . ' expense' . ($expense_count != 1 ? 's' : '');
                                ?>
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end"></div>
                    </div>
                </div>

                <!-- Expenses Table -->
                <div class="card-body">
                    <?php if (!empty($expenses)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo date('M j, Y', strtotime($expense['expense_date'])); ?></div>
                                            <small class="text-muted"><?php echo date('D', strtotime($expense['expense_date'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php 
                                                    // Decode any HTML entities stored in DB to avoid showing &amp; in UI
                                                    $cleanCategory = html_entity_decode($expense['category']);
                                                    echo htmlspecialchars(Helpers::humanize($cleanCategory)); 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($expense['description'])): ?>
                                                <?php echo htmlspecialchars($expense['description']); ?>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">No description</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-danger"><?php echo number_format($expense['amount'], 2); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?controller=expense&action=edit&id=<?php echo $expense['id']; ?>" 
                                                class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button type="button" class="btn btn-outline-danger delete-expense" 
                                                        data-id="<?php echo $expense['id']; ?>" 
                                                        data-description="<?php echo htmlspecialchars($expense['description'] ?: $expense['category']); ?>"
                                                        data-amount="<?php echo $expense['amount']; ?>"
                                                        title="Delete">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Expenses Found</h4>
                                <p class="text-muted mb-4">
                                    Start tracking your expenses today!
                                </p>
                                <a href="index.php?controller=expense&action=create" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Add Your First Expense
                                </a>
                            </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this expense?</p>
                <div class="alert alert-warning">
                    <strong id="expenseDescription"></strong>
                    <div class="mt-1">Amount: <span class="fw-bold text-danger" id="expenseAmount"></span></div>
                </div>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Delete Expense
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete expense functionality
    const deleteButtons = document.querySelectorAll('.delete-expense');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const expenseDescription = document.getElementById('expenseDescription');
    const expenseAmount = document.getElementById('expenseAmount');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const expenseId = this.getAttribute('data-id');
            const description = this.getAttribute('data-description');
            const amount = this.getAttribute('data-amount');
            
            expenseDescription.textContent = description;
            expenseAmount.textContent = parseFloat(amount).toFixed(2);
            
            // Set the delete URL
            confirmDeleteBtn.href = `index.php?controller=expense&action=delete&id=${expenseId}`;
            
            // Show the modal
            deleteModal.show();
        });
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>