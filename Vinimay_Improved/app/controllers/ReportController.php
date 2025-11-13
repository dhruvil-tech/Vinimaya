<?php
class ReportController {
    private $db;
    private $expense;
    private $category;

    public function __construct($database) {
        $this->db = $database->getConnection();
        $this->expense = new Expense($this->db);
        $this->category = new Category($this->db);
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        
        // Default to current month and year
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // Get monthly summary
        $monthly_summary = $this->expense->getMonthlySummary($user_id, $month, $year);
        
        // Get spending trend
        $spending_trend = $this->expense->getSpendingTrend($user_id, 12);
        
        // Get category-wise breakdown
        $category_stats = $this->category->getExpenseStats($user_id, $month, $year);
        
        // Get total spent
        $total_spent = $this->expense->getTotalSpent($user_id, $month, $year);
        
        // Prepare chart data
        $chart_data = $this->prepareChartData($monthly_summary, $spending_trend);
        
        include VIEW_PATH . '/reports/index.php';
    }

    private function prepareChartData($monthly_summary, $spending_trend) {
        $data = [
            'categories' => [],
            'amounts' => [],
            'colors' => [],
            'trend_months' => [],
            'trend_amounts' => []
        ];

        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'];
        $color_index = 0;

        // Pie chart data - statements are already executed by model methods
        while($row = $monthly_summary->fetch(PDO::FETCH_ASSOC)) {
            if($row['total'] > 0) {
                $data['categories'][] = $row['category'];
                $data['amounts'][] = $row['total'];
                $data['colors'][] = $colors[$color_index % count($colors)];
                $color_index++;
            }
        }

        // Trend chart data - statements are already executed by model methods
        while($row = $spending_trend->fetch(PDO::FETCH_ASSOC)) {
            $month_name = date('M Y', mktime(0, 0, 0, $row['month'], 1, $row['year']));
            $data['trend_months'][] = $month_name;
            $data['trend_amounts'][] = $row['total'] ?: 0;
        }

        return $data;
    }

    public function export() {
        $user_id = $_SESSION['user_id'];
        $format = $_GET['format'] ?? 'pdf';
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        // Get expenses for export
        $expenses = $this->expense->read($user_id, [
            'month' => $month,
            'year' => $year
        ]);

        // Get summary data
        $monthly_summary = $this->expense->getMonthlySummary($user_id, $month, $year);
        $total_spent = $this->expense->getTotalSpent($user_id, $month, $year);

        if ($format === 'excel') {
            $this->exportExcel($expenses, $monthly_summary, $total_spent, $month, $year);
        } else if ($format === 'csv') {
            $this->exportCSV($expenses, $monthly_summary, $total_spent, $month, $year);
        } else {
            $this->exportPDF($expenses, $monthly_summary, $total_spent, $month, $year);
        }
    }

    private function exportCSV($expenses, $monthly_summary, $total_spent, $month, $year) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="vinimaya_report_' . $month . '_' . $year . '.csv"');

        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, ['Vinimaya Expense Report - ' . date('F Y', mktime(0, 0, 0, $month, 1, $year))]);
        fputcsv($output, ['Total Spent: ₹' . number_format($total_spent, 2)]);
        fputcsv($output, []); // Empty line
        
        // Category summary - fetch as array first
        $summary_data = $monthly_summary->fetchAll(PDO::FETCH_ASSOC);
        fputcsv($output, ['Category', 'Amount', 'Percentage']);
        foreach($summary_data as $row) {
            $percentage = $total_spent > 0 ? ($row['total'] / $total_spent) * 100 : 0;
            fputcsv($output, [
                $row['category'],
                '₹' . number_format($row['total'], 2),
                number_format($percentage, 1) . '%'
            ]);
        }
        
        fputcsv($output, []); // Empty line
        
        // Detailed expenses - fetch as array first
        $expenses_data = $expenses->fetchAll(PDO::FETCH_ASSOC);
        fputcsv($output, ['Date', 'Category', 'Description', 'Amount']);
        foreach($expenses_data as $expense) {
            fputcsv($output, [
                $expense['expense_date'],
                $expense['category'] ?? '',
                $expense['description'] ?? '',
                '₹' . number_format($expense['amount'], 2)
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function exportPDF($expensesStmt, $monthly_summaryStmt, $total_spent, $month, $year) {
        // Fetch data as arrays before redirecting
        $expenses_data = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);
        $summary_data = $monthly_summaryStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Redirect to a print-friendly HTML view that the user can save as PDF
        $_SESSION['print_data'] = [
            'expenses' => $expenses_data,
            'summary' => $summary_data,
            'total_spent' => $total_spent,
            'month' => $month,
            'year' => $year
        ];
        
        header('Location: index.php?controller=reports&action=print');
        exit;
    }

    private function exportExcel($expensesStmt, $summaryStmt, $total_spent, $month, $year) {
        // Fetch data as arrays
        $expenses = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);
        $summary = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="vinimaya_report_' . $month . '_' . $year . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<html><head><meta charset=\"UTF-8\" /></head><body>";
        echo "<h3>Vinimaya Expense Report - " . date('F Y', mktime(0,0,0,$month,1,$year)) . "</h3>";
        echo "<p><strong>Total Spent:</strong> ₹" . number_format((float)$total_spent, 2) . "</p>";

        // Summary table
        echo "<h4>Category Summary</h4>";
        echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
        echo "<tr><th>Category</th><th>Amount (₹)</th></tr>";
        foreach ($summary as $row) {
            $cat = htmlspecialchars($row['category']);
            $amt = number_format((float)$row['total'], 2);
            echo "<tr><td>{$cat}</td><td>{$amt}</td></tr>";
        }
        echo "</table>";

        // Detailed expenses
        echo "<h4 style=\"margin-top:16px\">Detailed Expenses</h4>";
        echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
        echo "<tr><th>Date</th><th>Category</th><th>Description</th><th>Amount (₹)</th></tr>";
        foreach ($expenses as $exp) {
            $date = date('Y-m-d', strtotime($exp['expense_date']));
            $cat  = htmlspecialchars($exp['category']);
            $desc = htmlspecialchars($exp['description'] ?? '');
            $amt  = number_format((float)$exp['amount'], 2);
            echo "<tr><td>{$date}</td><td>{$cat}</td><td>{$desc}</td><td>{$amt}</td></tr>";
        }
        echo "</table>";

        echo "</body></html>";
        exit;
    }

    public function printView() {
        if (!isset($_SESSION['print_data'])) {
            header('Location: index.php?controller=reports&action=index');
            exit;
        }
        
        $data = $_SESSION['print_data'];
        unset($_SESSION['print_data']);
        
        include VIEW_PATH . '/reports/print.php';
    }
}
?>