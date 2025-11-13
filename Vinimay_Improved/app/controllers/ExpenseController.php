<?php
class ExpenseController {
    private $db;
    private $expense;
    private $category;

    public function __construct($database) {
        $this->db = $database->getConnection();
        $this->expense = new Expense($this->db);
        $this->category = new Category($this->db);
    }

    public function create() {
        $user_id = $_SESSION['user_id'];
        
        if($_POST) {
            try {
                $required = ['amount', 'category', 'expense_date'];
                foreach($required as $field) {
                    if(empty($_POST[$field])) {
                        throw new Exception("Please fill all required fields.");
                    }
                }

                $this->expense->user_id = $user_id;
                $this->expense->amount = Helpers::parseAmountINR($_POST['amount']);
                $this->expense->category = $_POST['category'];
                $this->expense->description = $_POST['description'] ?? '';
                $this->expense->expense_date = $_POST['expense_date'];

                if($this->expense->create()) {
                    $_SESSION['success'] = "Expense added successfully!";
                    header("Location: index.php?controller=expense&action=list");
                    exit();
                } else {
                    throw new Exception("Failed to add expense. Please try again.");
                }
            } catch(Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        $categories = $this->category->getAll();
        include VIEW_PATH . '/expenses/add.php';
    }

public function index() {
    $user_id = $_SESSION['user_id'];
    
    // Handle filters
    $filters = [];
    if (isset($_GET['month']) && !empty($_GET['month'])) {
        $filters['month'] = $_GET['month'];
        $filters['year'] = $_GET['year'] ?? date('Y');
    }
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $filters['category'] = $_GET['category'];
    }

    // Fetch as arrays for the view (avoid re-executing PDO in views)
    $expensesStmt = $this->expense->read($user_id, $filters);
    $expenses = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);

    $categoriesStmt = $this->category->getAll();
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

    $total_spent = $this->expense->getTotalSpent(
        $user_id,
        $filters['month'] ?? null,
        $filters['year'] ?? null
    );

    include VIEW_PATH . '/expenses/list.php';
}
    public function edit() {
        $user_id = $_SESSION['user_id'];
        $expense_id = $_GET['id'] ?? 0;

        if(!$this->expense->getById($expense_id, $user_id)) {
            $_SESSION['error'] = "Expense not found.";
            header("Location: index.php?controller=expense&action=list");
            exit();
        }

        if($_POST) {
            try {
                $required = ['amount', 'category', 'expense_date'];
                foreach($required as $field) {
                    if(empty($_POST[$field])) {
                        throw new Exception("Please fill all required fields.");
                    }
                }

                $this->expense->amount = Helpers::parseAmountINR($_POST['amount']);
                $this->expense->category = $_POST['category'];
                $this->expense->description = $_POST['description'] ?? '';
                $this->expense->expense_date = $_POST['expense_date'];

                if($this->expense->update()) {
                    $_SESSION['success'] = "Expense updated successfully!";
                    header("Location: index.php?controller=expense&action=list");
                    exit();
                } else {
                    throw new Exception("Failed to update expense. Please try again.");
                }
            } catch(Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }

        $categories = $this->category->getAll();
        include VIEW_PATH . '/expenses/edit.php';
    }

    public function delete() {
        $user_id = $_SESSION['user_id'];
        $expense_id = $_GET['id'] ?? 0;

        if($this->expense->getById($expense_id, $user_id)) {
            if($this->expense->delete()) {
                $_SESSION['success'] = "Expense deleted successfully!";
            } else {
                $_SESSION['error'] = "Failed to delete expense.";
            }
        } else {
            $_SESSION['error'] = "Expense not found.";
        }

        header("Location: index.php?controller=expense&action=list");
        exit();
    }
}
?>