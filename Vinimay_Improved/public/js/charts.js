// Charts initialization and management
class ChartManager {
    constructor() {
        this.charts = new Map();
        // Always initialize - chartData will be defined or we'll use empty data
        this.init();
    }

    init() {
        this.initCategoryChart();
        this.initTrendChart();
    }

    initCategoryChart() {
        const canvas = document.getElementById('categoryChart');
        const ctx = canvas ? canvas.getContext && canvas.getContext('2d') : null;
        const chartData = window.chartData || {};
        if (!ctx || !chartData || !chartData.categories || chartData.categories.length === 0) {
            console.log('No data for category chart');
            return;
        }

        try {
            this.charts.set('category', new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.categories,
                    datasets: [{
                        data: chartData.amounts,
                        backgroundColor: chartData.colors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    // ensure numeric values
                                    const value = Number(context.raw) || 0;
                                    const total = context.dataset.data.reduce((a, b) => (Number(a) || 0) + (Number(b) || 0), 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ₹${value.toLocaleString('en-IN')} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            }));
        } catch (error) {
            console.error('Error creating category chart:', error);
        }
    }

    initTrendChart() {
        const ctx = document.getElementById('trendChart');
        if (!ctx) {
            console.log('Trend chart canvas not found');
            return;
        }
        
        const chartData = window.chartData || {};
        const hasMonthlyData = chartData.trendMonths && chartData.trendMonths.length > 0;
        
        // Always show chart even if data is empty (to show structure)
        if (!hasMonthlyData) {
            console.log('No trend data available, showing empty chart');
            // Create empty chart to show structure
            this.charts.set('trend', new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Monthly Spending',
                        data: [],
                        borderColor: '#2c5aa0',
                        backgroundColor: 'rgba(44, 90, 160, 0.1)',
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }));
            return;
        }

        try {
            // Use monthly data
            const labels = chartData.trendMonths || [];
            const data = chartData.trendAmounts || [];
            const label = 'Monthly Spending';

            this.charts.set('trend', new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: '#2c5aa0',
                        backgroundColor: 'rgba(44, 90, 160, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2c5aa0',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Spent: ₹${Number(context.raw).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0
                            }
                        }
                    }
                }
            }));
        } catch (error) {
            console.error('Error creating trend chart:', error);
        }
    }

    destroy() {
        this.charts.forEach(chart => chart.destroy());
        this.charts.clear();
    }
}

// ✅ BETTER INITIALIZATION
document.addEventListener('DOMContentLoaded', function() {
    // Ensure chartData is defined
    if (typeof chartData === 'undefined') {
        window.chartData = {
            categories: [],
            amounts: [],
            colors: [],
            trendMonths: [],
            trendAmounts: []
        };
    }
    
    // Wait a bit to ensure Chart.js is loaded
    setTimeout(() => {
        if (typeof Chart !== 'undefined') {
            window.chartManager = new ChartManager();
        } else {
            console.error('Chart.js is not loaded');
        }
    }, 100);
});