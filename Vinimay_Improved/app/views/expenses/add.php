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
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="category" class="form-label fw-bold mb-0">Category <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="fas fa-plus me-1"></i>Add New Category
                                </button>
                            </div>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php 
                                // Fetch all categories into array to avoid re-execution issues
                                $categories_array = [];
                                while($cat = $categories->fetch()) {
                                    $categories_array[] = $cat;
                                }
                                foreach($categories_array as $cat): ?>
                                    <option value="<?php echo $cat['name']; ?>" 
                                        <?php echo (($_POST['category'] ?? '') == $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endforeach; ?>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newCategoryName" class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="newCategoryName" name="name" required 
                               placeholder="e.g., Gym, Subscription, etc.">
                        <div class="form-text">Enter a unique category name</div>
                    </div>
                    <div class="mb-3">
                        <label for="newCategoryIcon" class="form-label fw-bold">Icon (Optional)</label>
                        <input type="text" class="form-control" id="newCategoryIcon" name="icon" 
                               placeholder="e.g., 💪, 📺, etc." maxlength="2">
                        <div class="form-text">Enter an emoji or icon (default: 📦)</div>
                    </div>
                    <div id="categoryError" class="alert alert-danger d-none" role="alert"></div>
                    <div id="categorySuccess" class="alert alert-success d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Category creation functionality
document.addEventListener('DOMContentLoaded', function() {
    const addCategoryForm = document.getElementById('addCategoryForm');
    const categorySelect = document.getElementById('category');
    const categoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    const errorDiv = document.getElementById('categoryError');
    const successDiv = document.getElementById('categorySuccess');

    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide previous messages
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            const formData = {
                name: document.getElementById('newCategoryName').value.trim(),
                icon: document.getElementById('newCategoryIcon').value.trim() || '📦',
                budget_default: 0
            };

            if (!formData.name) {
                errorDiv.textContent = 'Category name is required.';
                errorDiv.classList.remove('d-none');
                return;
            }

            // Show loading state
            const submitBtn = addCategoryForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';

            fetch('index.php?controller=expense&action=createCategory', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new category to select dropdown
                    const option = document.createElement('option');
                    option.value = data.category.name;
                    option.textContent = data.category.name;
                    option.selected = true;
                    categorySelect.appendChild(option);
                    
                    // Show success message
                    successDiv.textContent = data.message;
                    successDiv.classList.remove('d-none');
                    
                    // Reset form
                    addCategoryForm.reset();
                    
                    // Close modal after a short delay
                    setTimeout(() => {
                        categoryModal.hide();
                        errorDiv.classList.add('d-none');
                        successDiv.classList.add('d-none');
                    }, 1500);
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('d-none');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Reset form when modal is closed
    const modalElement = document.getElementById('addCategoryModal');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            addCategoryForm.reset();
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
        });
    }
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
