<?php
class DashboardController {
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
        $current_month = date('m');
        $current_year = date('Y');

        // Get sidebar data
        $sidebar_data = $this->getSidebarData($user_id);
        
        // Get monthly summary
        $monthly_summary = $this->expense->getMonthlySummary($user_id, $current_month, $current_year);
        
        // Get total spent this month
        $total_spent = $this->expense->getTotalSpent($user_id, $current_month, $current_year);
        
        // Get spending trend
        $spending_trend = $this->expense->getSpendingTrend($user_id, 6);
        
        // Get category stats
        $category_stats = $this->category->getExpenseStats($user_id, $current_month, $current_year);
        
        // Get recent expenses
        $recent_expenses = $this->expense->read($user_id, ['limit' => 5]);
        
        // Prepare data for charts
        $chart_data = $this->prepareChartData($monthly_summary, $spending_trend);
        
        include VIEW_PATH . '/dashboard/index.php';
    }

    // Add this method to the existing DashboardController class
    private function getSidebarData($user_id) {
        $current_month = date('m');
        $current_year = date('Y');
    
    return [
        'total_spent' => $this->expense->getTotalSpent($user_id, $current_month, $current_year),
        'today_expenses' => $this->expense->read($user_id, [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d')
        ])
    ];
}
    private function prepareChartData($monthly_summary, $spending_trend) {
        $data = [
            'categories' => [],
            'amounts' => [],
            'colors' => [],
            'trend_months' => [],
            'trend_amounts' => []
        ];

        // Pie chart data
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'];
        $color_index = 0;

        $monthly_summary->execute();
        while($row = $monthly_summary->fetch()) {
            if($row['total'] > 0) {
                $data['categories'][] = $row['category'];
                $data['amounts'][] = $row['total'];
                $data['colors'][] = $colors[$color_index % count($colors)];
                $color_index++;
            }
        }

        // Trend chart data
        $spending_trend->execute();
        while($row = $spending_trend->fetch()) {
            $month_name = date('M Y', mktime(0, 0, 0, $row['month'], 1, $row['year']));
            $data['trend_months'][] = $month_name;
            $data['trend_amounts'][] = $row['total'] ?: 0;
        }

        return $data;
    }
}
?>