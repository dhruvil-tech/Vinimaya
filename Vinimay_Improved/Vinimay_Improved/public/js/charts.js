// Charts initialization and management
class ChartManager {
    constructor() {
        this.charts = new Map();
        // ✅ ADD CHECK FOR CHART DATA
        if (typeof chartData !== 'undefined') {
            this.init();
        }
    }

    init() {
        this.initCategoryChart();
        this.initTrendChart();
    }

    initCategoryChart() {
        const canvas = document.getElementById('categoryChart');
        const ctx = canvas ? canvas.getContext && canvas.getContext('2d') : null;
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
        if (!ctx || !chartData.trendMonths || chartData.trendMonths.length === 0) {
            console.log('No data for trend chart');
            return;
        }

        try {
            this.charts.set('trend', new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.trendMonths,
                    datasets: [{
                        label: 'Monthly Spending',
                        data: chartData.trendAmounts,
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
                                    return `Spent: ₹${context.raw.toLocaleString('en-IN')}`;
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
    // Wait a bit to ensure chartData is available
    setTimeout(() => {
        window.chartManager = new ChartManager();
    }, 100);
});