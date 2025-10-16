@extends('layouts.advanced-dashboard')

@section('page-title', 'Dashboard Overview')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Dashboard Overview</h2>
            <div class="text-muted">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('F d, Y') }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Total Users</div>
                        <div class="h3 mb-0">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Active Roles</div>
                        <div class="h3 mb-0">{{ \Spatie\Permission\Models\Role::count() }}</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-shield fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Permissions</div>
                        <div class="h3 mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-key fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Online Now</div>
                        <div class="h3 mb-0">1</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Activity -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fas fa-user-plus text-success me-2"></i>
                                    User Registration
                                </td>
                                <td>{{ Auth::user()->name }}</td>
                                <td>{{ Auth::user()->created_at->diffForHumans() }}</td>
                                <td><span class="badge bg-success">Success</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-sign-in-alt text-primary me-2"></i>
                                    Login
                                </td>
                                <td>{{ Auth::user()->name }}</td>
                                <td>Just now</td>
                                <td><span class="badge bg-primary">Active</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('create users')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New User
                    </a>
                    @endcan
                    
                    @can('create roles')
                    <a href="{{ route('roles.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create Role
                    </a>
                    @endcan
                    
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    System Information
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-1">
                        <span>Laravel Version:</span>
                        <span class="fw-bold">{{ app()->version() }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span>PHP Version:</span>
                        <span class="fw-bold">{{ PHP_VERSION }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span>Environment:</span>
                        <span class="fw-bold text-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                            {{ ucfirst(app()->environment()) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection