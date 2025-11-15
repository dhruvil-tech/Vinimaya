<?php
class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $name;
    public $icon;
    public $budget_default;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE is_active = 1 
                 ORDER BY name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getExpenseStats($user_id, $month, $year) {
        $query = "SELECT c.name, c.icon, 
                         COALESCE(SUM(e.amount), 0) as spent,
                         COUNT(e.id) as transaction_count
                  FROM " . $this->table . " c
                  LEFT JOIN expenses e ON c.name = e.category 
                         AND e.user_id = :user_id 
                         AND MONTH(e.expense_date) = :month 
                         AND YEAR(e.expense_date) = :year
                  WHERE c.is_active = 1
                  GROUP BY c.id, c.name, c.icon
                  ORDER BY spent DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":month", $month);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        
        return $stmt;
    }

    public function exists($name) {
        $query = "SELECT id FROM " . $this->table . " WHERE name = :name AND is_active = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create() {
        // Check if category already exists
        if ($this->exists($this->name)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " (name, icon, budget_default, is_active) 
                  VALUES (:name, :icon, :budget_default, 1)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags(trim($this->name)));
        $this->icon = $this->icon ? htmlspecialchars(strip_tags($this->icon)) : '📦';
        $this->budget_default = $this->budget_default ?? 0;
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":icon", $this->icon);
        $stmt->bindParam(":budget_default", $this->budget_default);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
}
?>

