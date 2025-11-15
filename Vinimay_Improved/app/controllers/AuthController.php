<?php
class AuthController {
    private $db;
    private $user;

    public function __construct($database) {
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register() {
        if($_POST) {
            try {
                $required = ['name', 'email', 'password', 'confirm_password', 'family_size'];
                foreach($required as $field) {
                    if(empty($_POST[$field])) {
                        throw new Exception("All fields are required.");
                    }
                }

                if($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Passwords do not match.");
                }

                if(strlen($_POST['password']) < 6) {
                    throw new Exception("Password must be at least 6 characters.");
                }

                if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format.");
                }

                $this->user->name = $_POST['name'];
                $this->user->email = $_POST['email'];
                $this->user->password_hash = User::hashPassword($_POST['password']);
                $this->user->family_size = $_POST['family_size'];
                $this->user->currency = 'INR';

                if($this->user->emailExists()) {
                    throw new Exception("Email already registered. Please login.");
                }

                if($this->user->register()) {
                    $_SESSION['success'] = "Registration successful! Please login.";
                    header("Location: index.php?controller=auth&action=login");
                    exit();
                } else {
                    throw new Exception("Registration failed. Please try again.");
                }
            } catch(Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        include VIEW_PATH . '/auth/register.php';
    }

    public function login() {
        if($_POST) {
            try {
                if(empty($_POST['email']) || empty($_POST['password'])) {
                    throw new Exception("Email and password are required.");
                }

                $this->user->email = $_POST['email'];
                $password = $_POST['password'];

                if($this->user->emailExists() && $this->user->verifyPassword($password)) {
                    $_SESSION['user_id'] = $this->user->id;
                    $_SESSION['user_name'] = $this->user->name;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['family_size'] = $this->user->family_size;
                    
                    $_SESSION['success'] = "Welcome back, " . $this->user->name . "!";
                    header("Location: index.php?controller=dashboard&action=index");
                    exit();
                } else {
                    throw new Exception("Invalid email or password.");
                }
            } catch(Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        include VIEW_PATH . '/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>