<?php
class Expense {
    private $conn;
    private $table = 'expenses';

    public $id;
    public $user_id;
    public $amount;
    public $category;
    public $description;
    public $expense_date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET user_id=:user_id, amount=:amount, category=:category, 
                     description=:description, expense_date=:expense_date";
        
        $stmt = $this->conn->prepare($query);
        
        $this->amount = floatval($this->amount);
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":expense_date", $this->expense_date);
        
        return $stmt->execute();
    }

    public function read($user_id, $filters = []) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id";
        
        // Add filters
        if(!empty($filters['month'])) {
            $query .= " AND MONTH(expense_date) = :month AND YEAR(expense_date) = :year";
        }
        if(!empty($filters['category'])) {
            $query .= " AND category = :category";
        }
        if(!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query .= " AND expense_date BETWEEN :start_date AND :end_date";
        }
        
        $query .= " ORDER BY expense_date DESC, created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if(!empty($filters['month'])) {
            $stmt->bindParam(":month", $filters['month']);
            $stmt->bindParam(":year", $filters['year']);
        }
        if(!empty($filters['category'])) {
            $stmt->bindParam(":category", $filters['category']);
        }
        if(!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $stmt->bindParam(":start_date", $filters['start_date']);
            $stmt->bindParam(":end_date", $filters['end_date']);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function getMonthlySummary($user_id, $month, $year) {
        $query = "SELECT category, SUM(amount) as total, COUNT(*) as count
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND MONTH(expense_date) = :month 
                  AND YEAR(expense_date) = :year 
                  GROUP BY category 
                  ORDER BY total DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":month", $month);
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        
        return $stmt;
    }

    public function getTotalSpent($user_id, $month = null, $year = null) {
        $query = "SELECT SUM(amount) as total 
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id";
        
        if($month && $year) {
            $query .= " AND MONTH(expense_date) = :month AND YEAR(expense_date) = :year";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if($month && $year) {
            $stmt->bindParam(":month", $month);
            $stmt->bindParam(":year", $year);
        }
        
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?: 0;
    }

    public function getSpendingTrend($user_id, $months = 6) {
        $query = "SELECT 
                    YEAR(expense_date) as year,
                    MONTH(expense_date) as month,
                    SUM(amount) as total,
                    COUNT(*) as count
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND expense_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                  GROUP BY YEAR(expense_date), MONTH(expense_date)
                  ORDER BY year, month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":months", $months, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET amount=:amount, category=:category, 
                     description=:description, expense_date=:expense_date
                 WHERE id=:id AND user_id=:user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->amount = floatval($this->amount);
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":expense_date", $this->expense_date);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE id=:id AND user_id=:user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        return $stmt->execute();
    }

    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE id = :id AND user_id = :user_id 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->amount = $row['amount'];
            $this->category = $row['category'];
            $this->description = $row['description'];
            $this->expense_date = $row['expense_date'];
            return true;
        }
        return false;
    }
}
?>