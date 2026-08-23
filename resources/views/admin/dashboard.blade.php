@extends('common.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: #a6b64a38;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e8e6d8;
        cursor: pointer;
    }

    .stat-card h4 {
        font-size: 15px;
        color: #2c3e50;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .stat-card .number {
        font-size: 36px;
        font-weight: 700;
        color: #2c3e50;
    }

    /* Charts Section */
    .charts-container {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 30px;
    }

    .chart-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .chart-card h3 {
        font-size: 18px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 25px;
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 300px;
    }

    .donut-chart-wrapper {
        position: relative;
        width: 250px;
        height: 250px;
        margin: 0 auto;
    }

    .donut-stats {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 50px;
        margin-top: 30px;
    }

    .donut-stat-item {
        text-align: center;
    }

    .donut-stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .donut-stat-label {
        font-size: 14px;
        color: #7f8c8d;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .donut-stat-label::before {
        content: '';
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
    }

    .donut-stat-label.delivered::before {
        background: #a8b456;
    }

    .donut-stat-label.assigned::before {
        background: #2c3e50;
    }

    @media (max-width: 1024px) {
        .charts-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid dashboard-stats">
    <!-- <div class="stat-card" id="totalActiveJobsCard">
        <h4>Total Active Jobs</h4>
        <div class="number" id="totalActiveJobs">0</div>
    </div> -->
    <div class="stat-card" id="completedDeliveriesCard">
        <h4>Completed Deliveries</h4>
        <div class="number" id="completedDeliveries">0</div>
    </div>
    <div class="stat-card" id="pendingPickupsCard">
        <h4>Pending Pickups</h4>
        <div class="number" id="pendingPickups">0</div>
    </div>
    <div class="stat-card" id="availableDriversCard">
        <h4>Available Drivers</h4>
        <div class="number" id="availableDrivers">0</div>
    </div>
    <div class="stat-card" id="inTransitOrdersCard">
        <h4>In-Transit Orders</h4>
        <div class="number" id="inTransitOrders">0</div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-container">
    <!-- Delivery Performance Report -->
    <div class="chart-card">
        <h3>Delivery Performance Report</h3>
        <div class="chart-wrapper">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    <!-- Delivery Activity Overview -->
    <div class="chart-card">
        <h3>Delivery Activity Overview</h3>
        <div class="donut-chart-wrapper">
            <canvas id="activityChart"></canvas>
        </div>
        <div class="donut-stats">
            <div class="donut-stat-item">
                <div class="donut-stat-number" id="deliveredCount">0</div>
                <div class="donut-stat-label delivered">Delivered</div>
            </div>
            <div class="donut-stat-item">
                <div class="donut-stat-number" id="assignedCount">0</div>
                <div class="donut-stat-label assigned">Assigned</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // const token = localStorage.getItem('api_token');
    const token = "{{ session('web_token') }}";

    // Fetch dashboard statistics
    async function fetchStatistics() {
        try {
            const response = await fetch('/api/v1/deliveries', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data) {
                const deliveries = data.data.deliveries || [];

                // Calculate statistics
                const totalActive = deliveries.filter(d => !['delivered', 'cancelled'].includes(d.status)).length;
                const completed = deliveries.filter(d => d.status === 'delivered').length;
                const pending = deliveries.filter(d => d.status === 'pending').length;
                const picked_up = deliveries.filter(d => d.status === 'picked_up').length;
                const inTransit = deliveries.filter(d => ['in_transit', 'picked_up'].includes(d.status)).length;

                //document.getElementById('totalActiveJobs').textContent = totalActive;
                document.getElementById('completedDeliveries').textContent = completed;
                document.getElementById('pendingPickups').textContent = picked_up;
                document.getElementById('inTransitOrders').textContent = inTransit;

                // Update donut chart stats
                const assigned = deliveries.filter(d => d.status === 'assigned').length;
                document.getElementById('deliveredCount').textContent = completed;
                document.getElementById('assignedCount').textContent = assigned;

                // Create charts
                createPerformanceChart();
                createActivityChart(completed, assigned);
            }
        } catch (error) {
            console.error('Error fetching statistics:', error);
        }
    }

    // Fetch driver statistics
    async function fetchDriverStatistics() {
        try {
            const response = await fetch('/api/v1/driver-profiles', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data) {
                let drivers = [];

                if (Array.isArray(data.data.profiles)) {
                    drivers = data.data.profiles;
                } else if (Array.isArray(data.data)) {
                    drivers = data.data;
                }
                // console.log('Drivers:', data.data);
                const available = drivers.filter(d => d.availability_status === 'available').length;
                document.getElementById('availableDrivers').textContent = available;
            }
        } catch (error) {
            console.error('Error fetching driver statistics:', error);
            document.getElementById('availableDrivers').textContent = '0';
        }
    }

    const ctx = document.getElementById('performanceChart');
    var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Deliveries',
                    // data: [15, 22, 28, 35, 40, 32, 38, 42, 36, 30, 25, 20],
                    data: [],
                    borderColor: '#2c3e50',
                    backgroundColor: 'rgba(44, 62, 80, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#2c3e50',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#2c3e50',
                    pointHoverBorderColor: '#ffffff',
                    borderWidth: 2
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
                        backgroundColor: '#2c3e50',
                        padding: 12,
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                size: 12
                            },
                            color: '#7f8c8d'
                        },
                        grid: {
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#7f8c8d'
                        },
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

    // Create Performance Chart (Line Chart)
    function createPerformanceChart() {
        const ctx = document.getElementById('performanceChart');
        if (!ctx) return;
        $.ajax({
            url: '/api/v1/deliveries-monthly',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                // console.log(response.data.monthly_deliveries);
                setTimeout(function() {
                    console.log(myChart.data.datasets[0].data[1]);
                    myChart.data.datasets[0].data[0] = response.data.monthly_deliveries['Jan'];
                    myChart.data.datasets[0].data[1] = response.data.monthly_deliveries['Feb'];
                    myChart.data.datasets[0].data[2] = response.data.monthly_deliveries['Mar'];
                    myChart.data.datasets[0].data[3] = response.data.monthly_deliveries['Apr'];
                    myChart.data.datasets[0].data[4] = response.data.monthly_deliveries['May'];
                    myChart.data.datasets[0].data[5] = response.data.monthly_deliveries['Jun'];
                    myChart.data.datasets[0].data[6] = response.data.monthly_deliveries['Jul'];
                    myChart.data.datasets[0].data[7] = response.data.monthly_deliveries['Aug'];
                    myChart.data.datasets[0].data[8] = response.data.monthly_deliveries['Sep'];
                    myChart.data.datasets[0].data[9] = response.data.monthly_deliveries['Oct'];
                    myChart.data.datasets[0].data[10] = response.data.monthly_deliveries['Nov'];
                    myChart.data.datasets[0].data[11] = response.data.monthly_deliveries['Dec'];

                    myChart.update(); 
                }, 2000);
            },
            error: function(xhr) {
                console.error(xhr.responseJSON);
            }
        });
    }

    // Create Activity Chart (Donut Chart)
    function createActivityChart(delivered, assigned) {
        const ctx = document.getElementById('activityChart');
        if (!ctx) return;

        const total = delivered + assigned;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Assigned'],
                datasets: [{
                    data: [delivered, assigned],
                    backgroundColor: ['#a8b456', '#2c3e50'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        padding: 12,
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        displayColors: true,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    ctx.save();
                    const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    // Total number
                    ctx.font = 'bold 42px Inter, sans-serif';
                    ctx.fillStyle = '#2c3e50';
                    ctx.fillText(total, centerX, centerY - 12);

                    // Label
                    ctx.font = '14px Inter, sans-serif';
                    ctx.fillStyle = '#7f8c8d';
                    ctx.fillText('Total Jobs', centerX, centerY + 18);

                    ctx.restore();
                }
            }]
        });
    }

    // Load data on page load
    fetchStatistics();
    fetchDriverStatistics();
    //redirect to jobs page when total active jobs card is clicked
    $('#totalActiveJobsCard, #pendingPickupsCard, #completedDeliveriesCard, #inTransitOrdersCard').click(function() {
        window.location.href = '/company/dashboard/deliveries';
    });
    $('#completedDeliveriesCard').click(function() {
        window.location.href = '/company/dashboard/deliveries?status=delivered';
    });
    $('#pendingPickupsCard').click(function() {
        window.location.href = '/company/dashboard/deliveries?status=picked_up';
    });
    $('#inTransitOrdersCard').click(function() {
        window.location.href = '/company/dashboard/deliveries?status=in_transit';
    });
    $('#availableDriversCard').click(function() {
        window.location.href = '/company/dashboard/drivers?status=available';
    });
         
</script>
@endsection
