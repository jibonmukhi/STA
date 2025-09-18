@extends('layouts.advanced-dashboard')

@section('page-title', 'My Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">My Reports</h3>
                            <p class="card-text opacity-75 mb-0">Personal analytics and company information overview</p>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50 small">Last Updated</div>
                            <div class="h6 mb-0">{{ now()->format('M d, Y - H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Companies</h6>
                            <h3 class="mb-0 text-primary">{{ $reportData['monthly_summary']['total_companies'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Ownership</h6>
                            <h3 class="mb-0 text-success">{{ $reportData['monthly_summary']['total_ownership'] }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Profile Complete</h6>
                            <h3 class="mb-0 text-warning">{{ $reportData['monthly_summary']['profile_completion'] }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Active Since</h6>
                            <h3 class="mb-0 text-info">{{ $reportData['monthly_summary']['active_since'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Company Breakdown -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Company Ownership Breakdown</h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')"><i class="fas fa-file-pdf me-2"></i>Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportReport('excel')"><i class="fas fa-file-excel me-2"></i>Export as Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportReport('csv')"><i class="fas fa-file-csv me-2"></i>Export as CSV</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($reportData['company_breakdown']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Role</th>
                                        <th>Ownership</th>
                                        <th>Joined</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData['company_breakdown'] as $company)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="fw-bold">{{ $company['name'] }}</div>
                                                @if($company['is_primary'])
                                                    <span class="badge bg-success ms-2">Primary</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $company['role'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-success"
                                                         style="width: {{ min($company['percentage'], 100) }}%"></div>
                                                </div>
                                                <span class="fw-bold">{{ $company['percentage'] }}%</span>
                                            </div>
                                        </td>
                                        <td>{{ $company['joined_date'] }}</td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Ownership Chart -->
                        <div class="mt-4">
                            <h6 class="mb-3">Ownership Distribution</h6>
                            <canvas id="ownershipChart" style="max-height: 300px;"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">No Companies</h6>
                            <p class="text-muted">You are not assigned to any companies yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Summary & Stats -->
        <div class="col-lg-4">
            <!-- Activity Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Summary</h5>
                </div>
                <div class="card-body">
                    <div class="activity-item d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-sign-in-alt text-primary me-3"></i>
                            <div>
                                <div class="fw-bold">Last Login</div>
                                <small class="text-muted">Account activity</small>
                            </div>
                        </div>
                        <span class="text-primary">{{ $reportData['activity_summary']['last_login'] }}</span>
                    </div>

                    <div class="activity-item d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-check text-success me-3"></i>
                            <div>
                                <div class="fw-bold">Account Status</div>
                                <small class="text-muted">Current status</small>
                            </div>
                        </div>
                        <span class="badge bg-success">{{ $reportData['activity_summary']['account_status'] }}</span>
                    </div>

                    <div class="activity-item d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building text-info me-3"></i>
                            <div>
                                <div class="fw-bold">Primary Company</div>
                                <small class="text-muted">Main affiliation</small>
                            </div>
                        </div>
                        <span class="text-info">{{ $reportData['activity_summary']['primary_company'] }}</span>
                    </div>

                    <div class="activity-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-warning me-3"></i>
                            <div>
                                <div class="fw-bold">Total Companies</div>
                                <small class="text-muted">Affiliated organizations</small>
                            </div>
                        </div>
                        <span class="badge bg-warning">{{ $reportData['activity_summary']['total_companies_joined'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monthly Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" style="max-height: 200px;"></canvas>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Profile Completion</span>
                            <span class="fw-bold">{{ $reportData['monthly_summary']['profile_completion'] }}%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar"
                                 style="width: {{ $reportData['monthly_summary']['profile_completion'] }}%"
                                 class="bg-{{ $reportData['monthly_summary']['profile_completion'] >= 80 ? 'success' : ($reportData['monthly_summary']['profile_completion'] >= 50 ? 'warning' : 'danger') }}"></div>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Updated {{ now()->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('user.certificate') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-certificate fa-2x mb-2 d-block"></i>
                                View Certificate
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <button onclick="generateReport()" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                                Generate Report
                            </button>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <button onclick="exportReport('pdf')" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-download fa-2x mb-2 d-block"></i>
                                Export Data
                            </button>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fas fa-home fa-2x mb-2 d-block"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/chartjs/chart.min.js') }}"></script>
<script>
    // Ownership Chart
    @if($reportData['company_breakdown']->count() > 0)
    const ownershipCtx = document.getElementById('ownershipChart').getContext('2d');
    new Chart(ownershipCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($reportData['company_breakdown'] as $company)
                '{{ $company['name'] }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($reportData['company_breakdown'] as $company)
                    {{ $company['percentage'] }},
                    @endforeach
                ],
                backgroundColor: [
                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    // Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Activity Score',
                data: [65, 75, 80, 78, 85, 90],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    function exportReport(format) {
        alert(`Exporting report as ${format.toUpperCase()}...\nThis feature would be implemented in a real application.`);
    }

    function generateReport() {
        alert('Generating comprehensive report...\nThis would compile all your data into a detailed document.');
    }
</script>
@endsection