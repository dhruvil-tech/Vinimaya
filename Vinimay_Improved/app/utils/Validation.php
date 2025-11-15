<?php
class Validation {
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    public static function validateAmount($amount) {
        return is_numeric($amount) && $amount > 0 && $amount <= 10000000; // 1 crore max
    }

    public static function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public static function validateCategory($category, $allowed_categories) {
        return in_array($category, $allowed_categories);
    }

    public static function validateFamilySize($size) {
        return is_numeric($size) && $size >= 1 && $size <= 20;
    }

    public static function getErrors() {
        return $_SESSION['validation_errors'] ?? [];
    }

    public static function addError($field, $message) {
        if (!isset($_SESSION['validation_errors'])) {
            $_SESSION['validation_errors'] = [];
        }
        $_SESSION['validation_errors'][$field] = $message;
    }

    public static function clearErrors() {
        unset($_SESSION['validation_errors']);
    }

    public static function hasErrors() {
        return !empty($_SESSION['validation_errors']);
    }

    public static function getError($field) {
        return $_SESSION['validation_errors'][$field] ?? '';
    }
}
?>