@extends('layouts.advanced-dashboard')

@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Welcome Section -->
    <div class="col-12">
        <div class="card border-0 bg-gradient-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                        <p class="mb-0 opacity-75">Here's what's happening with your platform today.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="fs-6 opacity-75">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ now()->format('l, F d, Y') }}
                        </div>
                        <div class="fs-5 fw-bold">
                            {{ now()->format('g:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">Total Users</div>
                        <div class="h2 mb-0 fw-bold text-primary">{{ \App\Models\User::count() }}</div>
                        <div class="text-success small">
                            <i class="fas fa-arrow-up me-1"></i>12% from last month
                        </div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-users fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">Active Roles</div>
                        <div class="h2 mb-0 fw-bold text-success">{{ \Spatie\Permission\Models\Role::count() }}</div>
                        <div class="text-info small">
                            <i class="fas fa-arrow-right me-1"></i>No change
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-user-shield fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">Permissions</div>
                        <div class="h2 mb-0 fw-bold text-info">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                        <div class="text-warning small">
                            <i class="fas fa-arrow-up me-1"></i>Updated recently
                        </div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-key fs-3 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">Online Now</div>
                        <div class="h2 mb-0 fw-bold text-warning">1</div>
                        <div class="text-success small">
                            <i class="fas fa-circle me-1"></i>You are online
                        </div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="fas fa-wifi fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Section -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">User Growth Analytics</h5>
                    <small class="text-muted">Monthly user registration trends</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Last 6 months
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Last 3 months</a></li>
                        <li><a class="dropdown-item" href="#">Last 6 months</a></li>
                        <li><a class="dropdown-item" href="#">Last year</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
                <small class="text-muted">Latest system activities</small>
            </div>
            <div class="card-body">
                <div class="activity-feed">
                    <div class="activity-item d-flex align-items-start mb-3">
                        <div class="activity-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-user-plus text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">New User Registered</div>
                            <div class="text-muted small">{{ Auth::user()->name }} joined the platform</div>
                            <div class="text-muted small">{{ Auth::user()->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="activity-item d-flex align-items-start mb-3">
                        <div class="activity-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-sign-in-alt text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">User Login</div>
                            <div class="text-muted small">{{ Auth::user()->name }} logged in</div>
                            <div class="text-muted small">Just now</div>
                        </div>
                    </div>

                    <div class="activity-item d-flex align-items-start mb-3">
                        <div class="activity-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-shield-alt text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Role Assigned</div>
                            <div class="text-muted small">Super Admin role assigned</div>
                            <div class="text-muted small">5 minutes ago</div>
                        </div>
                    </div>

                    <div class="activity-item d-flex align-items-start mb-3">
                        <div class="activity-icon bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-database text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">Database Seeded</div>
                            <div class="text-muted small">Roles and permissions created</div>
                            <div class="text-muted small">10 minutes ago</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary btn-sm">View All Activities</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @can('create users')
                    <div class="col-md-6">
                        <a href="{{ route('users.create') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center p-3">
                            <i class="fas fa-user-plus me-2"></i>
                            <div>
                                <div class="fw-semibold">Add User</div>
                                <small class="text-muted">Create new user account</small>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    @can('create roles')
                    <div class="col-md-6">
                        <a href="{{ route('roles.create') }}" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center p-3">
                            <i class="fas fa-plus-circle me-2"></i>
                            <div>
                                <div class="fw-semibold">Create Role</div>
                                <small class="text-muted">Setup new role & permissions</small>
                            </div>
                        </a>
                    </div>
                    @endcan
                    
                    <div class="col-md-6">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center p-3">
                            <i class="fas fa-user-cog me-2"></i>
                            <div>
                                <div class="fw-semibold">Edit Profile</div>
                                <small class="text-muted">Update your information</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-6">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center p-3">
                            <i class="fas fa-cog me-2"></i>
                            <div>
                                <div class="fw-semibold">Settings</div>
                                <small class="text-muted">System configuration</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-info me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fab fa-laravel text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Laravel</div>
                                <div class="text-muted small">v{{ app()->version() }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fab fa-php text-info"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">PHP</div>
                                <div class="text-muted small">v{{ PHP_VERSION }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-server text-success"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Environment</div>
                                <div class="text-muted small">{{ ucfirst(app()->environment()) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Uptime</div>
                                <div class="text-muted small">{{ now()->diffForHumans(now()->subHours(2)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <div class="row text-center">
                        <div class="col">
                            <div class="fw-bold text-success">99.9%</div>
                            <small class="text-muted">Uptime</small>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-primary">45ms</div>
                            <small class="text-muted">Response</small>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-info">{{ \App\Models\User::count() }}/∞</div>
                            <small class="text-muted">Users</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>Performance Metrics
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-3">
                                <canvas id="cpuChart" width="100" height="100"></canvas>
                            </div>
                            <h6 class="fw-semibold mb-1">CPU Usage</h6>
                            <p class="text-muted small mb-0">Current server load</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-3">
                                <canvas id="memoryChart" width="100" height="100"></canvas>
                            </div>
                            <h6 class="fw-semibold mb-1">Memory Usage</h6>
                            <p class="text-muted small mb-0">RAM consumption</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-3">
                                <canvas id="storageChart" width="100" height="100"></canvas>
                            </div>
                            <h6 class="fw-semibold mb-1">Storage Usage</h6>
                            <p class="text-muted small mb-0">Disk space utilization</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-3">
                                <canvas id="networkChart" width="100" height="100"></canvas>
                            </div>
                            <h6 class="fw-semibold mb-1">Network I/O</h6>
                            <p class="text-muted small mb-0">Data transfer rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Users',
                data: [10, 15, 13, 17, 20, 25],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Performance Charts (Doughnut)
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%'
    };

    // CPU Chart
    new Chart(document.getElementById('cpuChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [35, 65],
                backgroundColor: ['#4f46e5', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    ctx.save();
                    const fontSize = 18;
                    const fontStyle = 'bold';
                    ctx.font = fontStyle + ' ' + fontSize + 'px Inter';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#4f46e5';
                    ctx.fillText('35%', chart.width / 2, chart.height / 2 + 5);
                    ctx.restore();
                }
            }
        }
    });

    // Memory Chart
    new Chart(document.getElementById('memoryChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [68, 32],
                backgroundColor: ['#10b981', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    ctx.save();
                    const fontSize = 18;
                    const fontStyle = 'bold';
                    ctx.font = fontStyle + ' ' + fontSize + 'px Inter';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#10b981';
                    ctx.fillText('68%', chart.width / 2, chart.height / 2 + 5);
                    ctx.restore();
                }
            }
        }
    });

    // Storage Chart
    new Chart(document.getElementById('storageChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [45, 55],
                backgroundColor: ['#f59e0b', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    ctx.save();
                    const fontSize = 18;
                    const fontStyle = 'bold';
                    ctx.font = fontStyle + ' ' + fontSize + 'px Inter';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#f59e0b';
                    ctx.fillText('45%', chart.width / 2, chart.height / 2 + 5);
                    ctx.restore();
                }
            }
        }
    });

    // Network Chart
    new Chart(document.getElementById('networkChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [28, 72],
                backgroundColor: ['#0ea5e9', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                beforeDraw: function(chart) {
                    const ctx = chart.ctx;
                    ctx.save();
                    const fontSize = 18;
                    const fontStyle = 'bold';
                    ctx.font = fontStyle + ' ' + fontSize + 'px Inter';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#0ea5e9';
                    ctx.fillText('28%', chart.width / 2, chart.height / 2 + 5);
                    ctx.restore();
                }
            }
        }
    });
});
</script>
@endpush