<?php
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $family_size;
    public $currency;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function emailExists() {
        $query = "SELECT id, name, email, password_hash, family_size, currency 
                  FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->password_hash = $row['password_hash'];
            $this->family_size = $row['family_size'];
            $this->currency = $row['currency'];
            return true;
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                 SET name=:name, email=:email, password_hash=:password_hash, 
                     family_size=:family_size, currency=:currency";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->family_size = (int)$this->family_size;
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $this->password_hash);
        $stmt->bindParam(":family_size", $this->family_size);
        $stmt->bindParam(":currency", $this->currency);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->password_hash);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function updateProfile() {
        $query = "UPDATE " . $this->table . " 
                 SET name=:name, family_size=:family_size 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->family_size = (int)$this->family_size;
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":family_size", $this->family_size);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
}
?>