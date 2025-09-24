@extends('layouts.advanced-dashboard')

@section('page-title', __('dashboard.sta_manager_dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <h3 class="card-title mb-1">{{ __('dashboard.welcome_back') }}, {{ Auth::user()->name }}!</h3>
                    <p class="card-text opacity-75">{{ __('dashboard.sta_admin_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.total_users') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['total_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.active_users') }}</h6>
                            <h4 class="mb-0 text-success">{{ $stats['active_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.pending_approval') }}</h6>
                            <h4 class="mb-0 text-warning">{{ $stats['pending_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.total_companies') }}</h6>
                            <h4 class="mb-0 text-info">{{ $stats['total_companies'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals & Recent Activity -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('dashboard.pending_user_approvals') }}</h5>
                    <a href="{{ route('users.index', ['status' => 'parked']) }}" class="btn btn-outline-primary btn-sm">
                        {{ __('dashboard.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($pendingApprovals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.name') }}</th>
                                        <th>{{ __('dashboard.email') }}</th>
                                        <th>{{ __('dashboard.company') }}</th>
                                        <th>{{ __('dashboard.registered') }}</th>
                                        <th>{{ __('dashboard.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApprovals as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                                     class="avatar avatar-sm rounded-circle me-2">
                                                {{ $user->full_name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->primary_company->name ?? 'N/A' }}</td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}"
                                               class="btn btn-sm btn-outline-primary">{{ __('dashboard.review') }}</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">{{ __('dashboard.no_pending_approvals') }}</h6>
                            <p class="text-muted">{{ __('dashboard.all_registrations_up_to_date') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('dashboard.recent_users') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                            <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                     class="avatar avatar-sm rounded-circle me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $user->full_name }}</h6>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'parked' ? 'warning' : 'secondary') }}">
                                    @if($user->status === 'active')
                                        {{ __('dashboard.active') }}
                                    @elseif($user->status === 'parked')
                                        {{ __('dashboard.pending_approval') }}
                                    @else
                                        {{ __('dashboard.inactive') }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('dashboard.no_recent_users') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('dashboard.quick_actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('users.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.add_new_user') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('companies.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.add_company') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-user-shield fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.manage_roles') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('users.index', ['status' => 'parked']) }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.review_approvals') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection