<?php
class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function user() {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'family_size' => $_SESSION['family_size'] ?? null
        ];
    }

    public static function redirectIfNotAuthenticated($redirectTo = 'index.php?controller=auth&action=login') {
        if (!self::check()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header("Location: $redirectTo");
            exit;
        }
    }

    public static function redirectIfAuthenticated($redirectTo = 'index.php?controller=dashboard&action=index') {
        if (self::check()) {
            header("Location: $redirectTo");
            exit;
        }
    }

    public static function logout() {
        session_destroy();
        session_start();
    }

    public static function hasRole($role) {
        // For future multi-role implementation
        return true;
    }

    public static function storeRedirectUrl() {
        if (!self::check() && !in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        }
    }

    public static function getRedirectUrl($default = 'index.php?controller=dashboard&action=index') {
        $url = $_SESSION['redirect_url'] ?? $default;
        unset($_SESSION['redirect_url']);
        return $url;
    }
}
?>