// Expense form specific functionality
class ExpenseForm {
    constructor() {
        this.form = document.getElementById('expenseForm');
        if (this.form) {
            this.init();
        }
    }

    init() {
        this.setupRealTimeValidation();
        this.setupAutoSave();
        this.setupCategoryShortcuts();
        // this.setupQuickAmounts(); // ✅ ADD THIS
    }

    // // ✅ ADD THIS METHOD
    // setupQuickAmounts() {
    //     const quickAmountButtons = document.querySelectorAll('.quick-amount');
    //     const amountInput = document.getElementById('amount');
        
    //     if (amountInput && quickAmountButtons.length > 0) {
    //         quickAmountButtons.forEach(button => {
    //             button.addEventListener('click', (e) => {
    //                 e.preventDefault();
    //                 const amount = button.getAttribute('data-amount');
    //                 amountInput.value = amount;
    //                 amountInput.focus();
                    
    //                 // Show visual feedback
    //                 button.classList.add('btn-success');
    //                 button.classList.remove('btn-outline-primary');
    //                 setTimeout(() => {
    //                     button.classList.remove('btn-success');
    //                     button.classList.add('btn-outline-primary');
    //                 }, 500);
    //             });
    //         });
    //     }
    // }

    setupRealTimeValidation() {
        const amountInput = this.form.querySelector('#amount');
        const dateInput = this.form.querySelector('#expense_date');

        if (amountInput) {
            amountInput.addEventListener('input', this.validateAmount.bind(this));
        }

        if (dateInput) {
            dateInput.addEventListener('change', this.validateDate.bind(this));
        }
    }

    validateAmount(event) {
        const input = event.target;
        // Integer-only: keep digits, drop commas/₹/dots
        let raw = (input.value || '').toString().replace(/[^\d]/g, '');
        if (raw === '') {
            this.showFieldError(input, 'Enter a valid amount');
            return;
        }
        const value = parseInt(raw, 10);
        if (isNaN(value) || value < 0) {
            this.showFieldError(input, 'Enter a valid amount');
            return;
        }
        this.clearFieldError(input);
        input.value = String(value);
    }

    validateDate(event) {
        const input = event.target;
        const selectedDate = new Date(input.value);
        const today = new Date();
        
        if (selectedDate > today) {
            this.showFieldError(input, 'Cannot select future dates');
        } else {
            this.clearFieldError(input);
        }
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let feedback = field.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }

    setupAutoSave() {
        // Auto-save form data to localStorage
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', Vinimaya.debounce(() => {
                this.saveFormState();
            }, 1000));
        });

        // Load saved form state
        this.loadFormState();
    }

    saveFormState() {
        const formData = new FormData(this.form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        localStorage.setItem('vinimaya_draft_expense', JSON.stringify(data));
    }

    loadFormState() {
        const saved = localStorage.getItem('vinimaya_draft_expense');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                
                Object.keys(data).forEach(key => {
                    const input = this.form.querySelector(`[name="${key}"]`);
                    if (input && input.type !== 'submit' && input.type !== 'button') {
                        input.value = data[key];
                    }
                });

                // Show restore notification
                this.showRestoreNotification();
            } catch (e) {
                console.warn('Failed to load saved form data:', e);
            }
        }
    }

    showRestoreNotification() {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show mt-3';
        notification.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            We restored your unsaved expense. 
            <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="expenseForm.clearDraft()">
                Clear Draft
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        this.form.parentNode.insertBefore(notification, this.form.nextSibling);
    }

    clearDraft() {
        localStorage.removeItem('vinimaya_draft_expense');
        this.form.reset();
        Vinimaya.showToast('Draft cleared', 'success');
        
        // Remove notification
        const alert = document.querySelector('.alert-info');
        if (alert) {
            alert.remove();
        }
    }

    setupCategoryShortcuts() {
        // Add keyboard shortcuts for common categories
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.altKey) {
                const categorySelect = this.form.querySelector('#category');
                if (categorySelect) {
                    switch(e.key) {
                        case '1':
                            this.selectCategory(categorySelect, 'Groceries & Ration');
                            break;
                        case '2':
                            this.selectCategory(categorySelect, 'Transport & Fuel');
                            break;
                        case '3':
                            this.selectCategory(categorySelect, 'Utility Bills');
                            break;
                        case '4':
                            this.selectCategory(categorySelect, 'Medical');
                            break;
                    }
                }
            }
        });
    }

    selectCategory(select, categoryName) {
        for (let option of select.options) {
            if (option.text.includes(categoryName)) {
                option.selected = true;
                Vinimaya.showToast(`Category set to: ${categoryName}`, 'info');
                break;
            }
        }
    }
}

// Initialize expense form when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.expenseForm = new ExpenseForm();
});

// Handle form reset to clear draft
document.addEventListener('DOMContentLoaded', function() {
    const resetButtons = document.querySelectorAll('button[type="reset"]');
    resetButtons.forEach(button => {
        button.addEventListener('click', function() {
            localStorage.removeItem('vinimaya_draft_expense');
            Vinimaya.showToast('Form reset', 'info');
        });
    });
});