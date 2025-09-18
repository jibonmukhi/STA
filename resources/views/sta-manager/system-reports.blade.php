@extends('layouts.advanced-dashboard')

@section('page-title', 'System Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>System Reports & Analytics
            </h4>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
                <button type="button" class="btn btn-outline-info">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Display Flash Messages -->
@include('components.flash-messages')

<!-- System Statistics Overview -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="mb-0">{{ number_format($stats['total_users']) }}</h2>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Active Users</h5>
                        <h2 class="mb-0">{{ number_format($stats['active_users']) }}</h2>
                        <small class="opacity-75">{{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100, 1) : 0 }}% of total</small>
                    </div>
                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Pending Users</h5>
                        <h2 class="mb-0">{{ number_format($stats['parked_users']) }}</h2>
                        <small class="opacity-75">Awaiting approval</small>
                    </div>
                    <i class="fas fa-user-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Total Companies</h5>
                        <h2 class="mb-0">{{ number_format($stats['total_companies']) }}</h2>
                        <small class="opacity-75">{{ number_format($stats['active_companies']) }} active</small>
                    </div>
                    <i class="fas fa-building fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Distribution by Role -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-shield me-2"></i>Users by Role Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="roleChart" height="300"></canvas>
                <div class="mt-3">
                    @foreach($usersByRole as $role => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-{{ $role === 'sta_manager' ? 'primary' : ($role === 'company_manager' ? 'success' : 'info') }}">
                                {{
                                    match($role) {
                                        'sta_manager' => 'STA Manager',
                                        'company_manager' => 'Company Manager',
                                        'end_user' => 'End User',
                                        'no_role' => 'No Role Assigned',
                                        default => ucwords(str_replace('_', ' ', $role))
                                    }
                                }}
                            </span>
                            <strong>{{ $count }} users</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- User Registrations Timeline -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>User Registrations (Last 12 Months)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="registrationChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Company Statistics -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $companyStats->total_companies ?? 0 }}</h4>
                        <small class="text-muted">Total Companies</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $companyStats->active_companies ?? 0 }}</h4>
                        <small class="text-muted">Active Companies</small>
                    </div>
                </div>
                <hr>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success"
                         style="width: {{ $companyStats->total_companies > 0 ? round(($companyStats->active_companies / $companyStats->total_companies) * 100) : 0 }}%">
                    </div>
                </div>
                <small class="text-muted">
                    {{ $companyStats->total_companies > 0 ? round(($companyStats->active_companies / $companyStats->total_companies) * 100) : 0 }}% of companies are active
                </small>
            </div>
        </div>
    </div>

    <!-- Recent User Activity -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Recent User Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivity as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title bg-light text-dark rounded-circle">
                                                {{ substr($user->full_name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $user->full_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->roles->first()?->name === 'sta_manager' ? 'primary' : ($user->roles->first()?->name === 'company_manager' ? 'success' : 'info') }}">
                                        {{ $user->formatted_role }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->companies->count() > 0)
                                        <span class="badge bg-secondary">
                                            {{ $user->companies->first()->name }}
                                            @if($user->companies->count() > 1)
                                                +{{ $user->companies->count() - 1 }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($user->status === 'parked')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->created_at->diffForHumans() }}
                                    </small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No recent activity found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tools me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('users.pending.approvals') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-user-clock me-2"></i>
                            Review Pending Users
                            @if($stats['parked_users'] > 0)
                                <span class="badge bg-warning ms-2">{{ $stats['parked_users'] }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('companies.index') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-building me-2"></i>Manage Companies
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-user-shield me-2"></i>Manage Roles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Role Distribution Pie Chart
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    const roleData = @json($usersByRole);

    const roleLabels = Object.keys(roleData).map(role => {
        switch(role) {
            case 'sta_manager': return 'STA Manager';
            case 'company_manager': return 'Company Manager';
            case 'end_user': return 'End User';
            case 'no_role': return 'No Role Assigned';
            default: return role.replace('_', ' ').replace(/\w\S*/g, (txt) =>
                txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
            );
        }
    });

    const roleValues = Object.values(roleData);
    const roleColors = ['#0d6efd', '#198754', '#0dcaf0', '#6c757d', '#dc3545'];

    new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: roleLabels,
            datasets: [{
                data: roleValues,
                backgroundColor: roleColors.slice(0, roleValues.length),
                borderWidth: 2,
                borderColor: '#fff'
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
                }
            }
        }
    });

    // User Registration Timeline Chart
    const registrationCtx = document.getElementById('registrationChart').getContext('2d');
    const registrationData = @json($userRegistrationsByMonth);

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    const labels = registrationData.map(item => `${months[item.month - 1]} ${item.year}`);
    const values = registrationData.map(item => item.count);

    new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'User Registrations',
                data: values,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
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
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection