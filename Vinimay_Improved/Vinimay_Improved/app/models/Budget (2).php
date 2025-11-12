<?php
class Budget {
    private $conn;
    private $table = 'budgets';

    public $id;
    public $user_id;
    public $category_id;
    public $monthly_limit;
    public $current_month;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setBudget($user_id, $category_id, $monthly_limit, $yearmonth = null) {
        if (!$yearmonth) {
            $yearmonth = date('Ym');
        }

        $query = "INSERT INTO " . $this->table . " 
                 (user_id, category_id, monthly_limit, current_month) 
                 VALUES (:user_id, :category_id, :monthly_limit, :current_month)
                 ON DUPLICATE KEY UPDATE monthly_limit = :monthly_limit";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":monthly_limit", $monthly_limit);
        $stmt->bindParam(":current_month", $yearmonth);

        return $stmt->execute();
    }

    public function getUserBudgets($user_id, $yearmonth = null) {
        if (!$yearmonth) {
            $yearmonth = date('Ym');
        }

        $query = "SELECT b.*, c.name as category_name, c.icon,
                         COALESCE(SUM(e.amount), 0) as spent
                  FROM " . $this->table . " b
                  JOIN categories c ON b.category_id = c.id
                  LEFT JOIN expenses e ON b.user_id = e.user_id 
                         AND c.name = e.category 
                         AND DATE_FORMAT(e.expense_date, '%Y%m') = b.current_month
                  WHERE b.user_id = :user_id AND b.current_month = :current_month
                  GROUP BY b.id, c.name, c.icon
                  ORDER BY c.name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":current_month", $yearmonth);
        $stmt->execute();

        return $stmt;
    }

    public function getBudgetProgress($user_id, $yearmonth = null) {
        if (!$yearmonth) {
            $yearmonth = date('Ym');
        }

        $query = "SELECT c.name as category, c.icon,
                         COALESCE(b.monthly_limit, 0) as budget_limit,
                         COALESCE(SUM(e.amount), 0) as spent,
                         CASE 
                            WHEN COALESCE(b.monthly_limit, 0) = 0 THEN 0
                            ELSE (COALESCE(SUM(e.amount), 0) / b.monthly_limit) * 100 
                         END as percentage
                  FROM categories c
                  LEFT JOIN " . $this->table . " b ON c.id = b.category_id 
                         AND b.user_id = :user_id 
                         AND b.current_month = :current_month
                  LEFT JOIN expenses e ON c.name = e.category 
                         AND e.user_id = :user_id 
                         AND DATE_FORMAT(e.expense_date, '%Y%m') = :current_month
                  WHERE c.is_active = 1
                  GROUP BY c.id, c.name, c.icon, b.monthly_limit
                  HAVING budget_limit > 0 OR spent > 0
                  ORDER BY percentage DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":current_month", $yearmonth);
        $stmt->execute();

        return $stmt;
    }
}
?>