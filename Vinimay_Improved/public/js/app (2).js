// Main Application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    initializeDeleteHandlers();
    initializeQuickAmounts();
    initializeFormValidation();
    initializeDateHandlers();
}

// Delete expense confirmation
function initializeDeleteHandlers() {
    const deleteButtons = document.querySelectorAll('.delete-expense');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const expenseDescription = document.getElementById('expenseDescription');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const expenseId = this.getAttribute('data-id');
            const description = this.getAttribute('data-description');
            
            expenseDescription.textContent = description;
            confirmDeleteBtn.href = `index.php?controller=expense&action=delete&id=${expenseId}`;
            deleteModal.show();
        });
    });
}

// Quick amount buttons
function initializeQuickAmounts() {
    const quickAmountButtons = document.querySelectorAll('.quick-amount');
    const amountInput = document.getElementById('amount');
    
    if (amountInput && quickAmountButtons.length > 0) {
        quickAmountButtons.forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                amountInput.value = amount;
                amountInput.focus();
            });
        });
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[method="POST"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    highlightFieldError(field);
                } else {
                    removeFieldError(field);
                }
            });
            
            // Amount validation
            const amountField = this.querySelector('input[name="amount"]');
            if (amountField && amountField.value) {
                const amount = parseFloat(amountField.value);
                if (amount <= 0) {
                    isValid = false;
                    highlightFieldError(amountField, 'Amount must be greater than 0');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showToast('Please fill all required fields correctly.', 'error');
            }
        });
    });
}

function highlightFieldError(field, message = 'This field is required') {
    field.classList.add('is-invalid');
    
    let feedback = field.nextElementSibling;
    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        field.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

function removeFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
}

// Date handlers
function initializeDateHandlers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set max date to today
        input.max = new Date().toISOString().split('T')[0];
    });
}

// Toast notifications
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after hide
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2
    }).format(amount);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export for use in other modules
window.Vinimaya = {
    formatCurrency,
    showToast,
    debounce
};