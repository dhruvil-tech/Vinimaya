<?php

session_start();

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Define base URL for assets
// This calculates the base URL dynamically based on where the script is located
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_url = ($script_dir === '/' || $script_dir === '\\') ? '' : $script_dir;
// Remove 'public' from the path if present (since we're in public/index.php)
$base_url = str_replace('/public', '', $base_url);
$base_url = rtrim(str_replace('\\', '/', $base_url), '/');
define('BASE_URL', $base_url);

// Rest of your code...


// Auto-load classes
spl_autoload_register(function ($class_name) {
    $paths = [
        APP_PATH . '/models/' . $class_name . '.php',
        APP_PATH . '/controllers/' . $class_name . '.php',
        APP_PATH . '/utils/' . $class_name . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Initialize database connection
try {
    require_once APP_PATH . '/config/database.php';
    $database = new Database();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get controller and action from URL
$controller_name = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Route the request
try {
    switch($controller_name) {
        case 'auth':
            $controller = new AuthController($database);
            switch($action) {
                case 'register':
                    $controller->register();
                    break;
                case 'login':
                    $controller->login();
                    break;
                case 'logout':
                    $controller->logout();
                    break;
                default:
                    header("Location: index.php?controller=auth&action=login");
                    exit;
            }
            break;
            
        case 'expense':
            // Check authentication
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            $controller = new ExpenseController($database);
            switch($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'list':
                    $controller->index();
                    break;
                case 'edit':
                    $controller->edit();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                default:
                    header("Location: index.php?controller=expense&action=list");
                    exit;
            }
            break;
            
        case 'reports':
            // Check authentication
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            $controller = new ReportController($database);
            switch($action) {
                case 'index':
                    $controller->index();
                    break;
                case 'export':
                    $controller->export();
                    break;
                case 'print':
                    $controller->printView();
                    break;
                default:
                    header("Location: index.php?controller=reports&action=index");
                    exit;
            }
            break;
            
        case 'dashboard':
            // Check authentication
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            $controller = new DashboardController($database);
            $controller->index();
            break;
            
        default:
            // Homepage - check if user is logged in
            if (isset($_SESSION['user_id'])) {
                header("Location: index.php?controller=dashboard&action=index");
                exit;
            } else {
                include APP_PATH . '/views/home.php';
            }
            break;
    }
} catch (Exception $e) {
    // Log error
    error_log("Application error: " . $e->getMessage());
    
    // Show user-friendly error page
    http_response_code(500);
    include APP_PATH . '/views/errors/500.php';
    exit;
}
?>