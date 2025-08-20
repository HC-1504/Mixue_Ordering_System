<?php require_once __DIR__ . '/../_header.php'; ?>

<style>
    /* Time filter button styles */
    .btn-group .btn {
        border-radius: 0.375rem;
        margin-right: 2px;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        margin-right: 0;
    }
    
    .btn-group .btn.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
    .chart-pie, .chart-area {
        position: relative;
    }
    .no-data-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #858796;
        font-size: 1.2rem;
        text-align: center;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Reports Dashboard</h1>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="generateReportWithFilter('1week')">1 Week</button>
                        <button type="button" class="btn btn-outline-primary" onclick="generateReportWithFilter('1month')">1 Month</button>
                        <button type="button" class="btn btn-outline-primary" onclick="generateReportWithFilter('3months')">3 Months</button>
                        <button type="button" class="btn btn-outline-primary active" onclick="generateReportWithFilter('all')">All Time</button>
                    </div>
                    <button class="btn btn-primary" onclick="generateBusinessReport()">
                        <i class="fas fa-sync-alt"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSales">$0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Order Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgOrderValue">$0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProducts">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Order Status Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="orderStatusChart"></canvas>
                        <div id="orderStatusNoData" class="no-data-overlay" style="display: none;">No Data Available</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Sales Trend -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Sales Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailySalesChart"></canvas>
                        <div id="dailySalesNoData" class="no-data-overlay" style="display: none;">No Data Available</div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Order Details Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders (via API)</h6>
                </div>
                <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                        <th>Branch</th>
                                    <th>Date</th>
                                    </tr>
                                </thead>
                            <tbody id="ordersTableBody">
                                <!-- Data will be populated via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let orderStatusChart, dailySalesChart;

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    generateBusinessReport();
});

function initializeCharts() {
    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    orderStatusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02424'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    // Daily Sales Chart
    const salesCtx = document.getElementById('dailySalesChart').getContext('2d');
    dailySalesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Daily Sales',
                data: [],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#4e73df',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#4e73df',
                pointHoverBorderColor: '#4e73df',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        parser: 'MM/DD/YYYY HH:mm'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return '$' + value.toFixed(2);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': $' + tooltipItem.yLabel.toFixed(2);
                    }
                }
            }
        }
    });
}

function generateReportWithFilter(period) {
    // Show loading state
    document.getElementById('totalSales').textContent = 'Loading...';
    document.getElementById('totalOrders').textContent = 'Loading...';
    document.getElementById('avgOrderValue').textContent = 'Loading...';
    document.getElementById('totalProducts').textContent = 'Loading...';

    // Calculate date range based on period
    let startDate = null;
    let endDate = null;
    
    // Use the current date for dynamic filtering
    const today = new Date();
    endDate = today.toISOString().split('T')[0];
    
    switch(period) {
        case '1week':
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            startDate = weekAgo.toISOString().split('T')[0];
            break;
        case '1month':
            const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
            startDate = monthAgo.toISOString().split('T')[0];
            break;
        case '3months':
            const threeMonthsAgo = new Date(today.getTime() - 90 * 24 * 60 * 60 * 1000);
            startDate = threeMonthsAgo.toISOString().split('T')[0];
            break;
        case 'all':
            startDate = null;
            endDate = null;
            break;
    }

    // Debug log
    console.log('Filtering with period:', period, 'startDate:', startDate, 'endDate:', endDate);
    
    // Make AJAX call to generate report with date filter
    fetch('reports.php?action=generate_report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            period: period
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateDashboard(data.data);
            // Update active button
            document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
            // Find the clicked button and make it active
            const clickedButton = document.querySelector(`[onclick="generateReportWithFilter('${period}')"]`);
            if (clickedButton) {
                clickedButton.classList.add('active');
            }
        } else {
            console.error('Error generating report:', data.message);
            alert('Error generating report: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating report. Please try again.');
    });
}

function generateBusinessReport() {
    // Show loading state
    document.getElementById('totalSales').textContent = 'Loading...';
    document.getElementById('totalOrders').textContent = 'Loading...';
    document.getElementById('avgOrderValue').textContent = 'Loading...';
    document.getElementById('totalProducts').textContent = 'Loading...';

    // Make AJAX call to generate report
    fetch('reports.php?action=generate_report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateDashboard(data.data);
        } else {
            console.error('Error generating report:', data.message);
            alert('Error generating report: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating report. Please try again.');
    });
}

function updateDashboard(data) {
    console.log('Dashboard data received:', data); // Debug log

    const hasData = data.sales_summary && data.sales_summary.total_orders > 0;

    // Update summary cards
    document.getElementById('totalSales').textContent = '$' + (hasData ? data.sales_summary.total_sales.toFixed(2) : '0.00');
    document.getElementById('totalOrders').textContent = hasData ? data.sales_summary.total_orders : '0';
    document.getElementById('avgOrderValue').textContent = '$' + (hasData ? data.sales_summary.average_order_value.toFixed(2) : '0.00');
    document.getElementById('totalProducts').textContent = data.products_count || '0';

    // Toggle visibility of no-data messages
    document.getElementById('orderStatusNoData').style.display = hasData ? 'none' : 'block';
    document.getElementById('dailySalesNoData').style.display = hasData ? 'none' : 'block';
    document.getElementById('orderStatusChart').style.display = hasData ? 'block' : 'none';
    document.getElementById('dailySalesChart').style.display = hasData ? 'block' : 'none';

    if (hasData) {
        // Update order status chart
        const statusLabels = Object.keys(data.order_status_summary.status_breakdown);
        const statusData = Object.values(data.order_status_summary.status_breakdown).map(item => item.count);
        
        orderStatusChart.data.labels = statusLabels;
        orderStatusChart.data.datasets[0].data = statusData;
        orderStatusChart.update();

        // Update daily sales chart
        const dailyLabels = Object.keys(data.sales_summary.daily_sales);
        const dailyData = Object.values(data.sales_summary.daily_sales);
        
        dailySalesChart.data.labels = dailyLabels;
        dailySalesChart.data.datasets[0].data = dailyData;
        dailySalesChart.update();
    } else {
        // Clear chart data if no orders
        orderStatusChart.data.labels = [];
        orderStatusChart.data.datasets[0].data = [];
        orderStatusChart.update();

        dailySalesChart.data.labels = [];
        dailySalesChart.data.datasets[0].data = [];
        dailySalesChart.update();
    }

    // Update orders table
    const orders = (hasData && data.sales_summary.orders) ? data.sales_summary.orders : [];
    updateOrdersTable(orders);
}

function updateOrdersTable(orders) {
    console.log('Updating orders table with:', orders); // Debug log
    
    const tbody = document.getElementById('ordersTableBody');
    tbody.innerHTML = '';

    if (!orders || orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
        return;
    }

    orders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${order.id}</td>
            <td>${order.customer_name || 'N/A'}</td>
            <td>$${parseFloat(order.total).toFixed(2)}</td>
            <td><span class="badge ${getStatusBadgeClass(order.status)}">${order.status}</span></td>
            <td>${order.branch_name || 'N/A'}</td>
            <td>${new Date(order.created_at).toLocaleDateString()}</td>
        `;
        tbody.appendChild(row);
    });
}

function getStatusBadgeClass(status) {
    const statusClasses = {
        'Pending': 'bg-warning',
        'Preparing': 'bg-info',
        'Out for Delivery': 'bg-primary',
        'Completed': 'bg-success',
        'Cancelled': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}
</script>

<?php require_once __DIR__ . '/../_footer.php'; ?>
