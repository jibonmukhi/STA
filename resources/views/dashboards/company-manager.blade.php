@extends('layouts.advanced-dashboard')

@section('page-title', __('dashboard.company_manager_dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body">
                    <h3 class="card-title mb-1">{{ __('dashboard.welcome_back') }}, {{ Auth::user()->name }}!</h3>
                    <p class="card-text opacity-75">{{ __('dashboard.company_admin_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.my_companies') }}</h6>
                            <h4 class="mb-0 text-info">{{ $stats['my_companies'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('dashboard.company_users') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['company_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
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
                            <h4 class="mb-0 text-success">{{ $stats['active_company_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Companies & Recent Users -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('dashboard.my_companies') }}</h5>
                    <a href="{{ route('my-companies.index') }}" class="btn btn-outline-primary btn-sm">
                        {{ __('dashboard.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($userCompanies->count() > 0)
                        <div class="row">
                            @foreach($userCompanies as $company)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                                     class="avatar avatar-lg rounded me-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $company->name }}</h6>
                                                    <p class="text-muted small mb-1">{{ $company->email }}</p>
                                                    <span class="badge bg-{{ $company->active ? 'success' : 'secondary' }}">
                                                        {{ $company->active ? __('dashboard.active') : __('dashboard.inactive') }}
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-muted small">{{ __('dashboard.users') }}</div>
                                                    <div class="h5 mb-0">{{ $company->users->count() }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="row text-center">
                                                    <div class="col">
                                                        <div class="text-muted small">{{ __('dashboard.role') }}</div>
                                                        <div class="fw-bold">{{ $company->pivot->role_in_company ?? 'Member' }}</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-muted small">{{ __('dashboard.ownership') }}</div>
                                                        <div class="fw-bold">{{ $company->pivot->percentage ?? 0 }}%</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-muted small">{{ __('dashboard.primary') }}</div>
                                                        <div class="fw-bold">
                                                            @if($company->pivot->is_primary)
                                                                <i class="fas fa-check text-success"></i>
                                                            @else
                                                                <i class="fas fa-times text-muted"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">{{ __('dashboard.no_companies_assigned') }}</h6>
                            <p class="text-muted">{{ __('dashboard.contact_admin_for_companies') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('dashboard.recent_company_users') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentCompanyUsers->count() > 0)
                        @foreach($recentCompanyUsers as $user)
                            <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                     class="avatar avatar-sm rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
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
                            <a href="{{ route('company-users.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.add_company_user') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.manage_users') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('my-companies.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.my_companies') }}
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('company.dashboard') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                                {{ __('dashboard.view_reports') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection