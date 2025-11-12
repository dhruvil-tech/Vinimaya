<?php
class Helpers {
    public static function formatCurrency($amount, $currency = 'INR') {
        return number_format($amount, 2);
    }

    public static function parseAmountINR($input) {
        // Allow digits, commas, dots, minus; strip everything else (₹, spaces, letters, etc.)
        $clean = preg_replace('/[^\d.,-]/', '', (string)$input);
        // Remove thousand separators (commas); keep decimal dot
        $clean = str_replace(',', '', $clean);
        return (float)$clean;
    }

    public static function humanize($text) {
        $text = str_replace(['_', '-'], ' ', (string)$text);
        $text = strtolower(trim($text));
        return ucwords($text);
    }

    public static function formatDate($date, $format = 'M j, Y') {
        return date($format, strtotime($date));
    }

    public static function getMonthName($monthNumber) {
        return date('F', mktime(0, 0, 0, $monthNumber, 1));
    }

    public static function getMonthsList() {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = self::getMonthName($i);
        }
        return $months;
    }

    public static function getYearsList($start = 2020, $end = null) {
        if (!$end) {
            $end = date('Y');
        }
        
        $years = [];
        for ($i = $end; $i >= $start; $i--) {
            $years[$i] = $i;
        }
        return $years;
    }

    public static function calculatePercentage($part, $total) {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, 1);
    }

    public static function getProgressColor($percentage) {
        if ($percentage < 60) return 'success';
        if ($percentage < 80) return 'warning';
        return 'danger';
    }

    public static function redirect($url) {
        header("Location: $url");
        exit;
    }

    public static function getCurrentUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
               "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        
        if (empty($text)) {
            return 'n-a';
        }
        
        return $text;
    }

    public static function arrayToOptions($array, $selected = '') {
        $options = '';
        foreach ($array as $key => $value) {
            $isSelected = ($key == $selected) ? 'selected' : '';
            $options .= "<option value=\"$key\" $isSelected>$value</option>";
        }
        return $options;
    }

    public static function debug($data, $die = false) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($die) die();
    }
}
?>