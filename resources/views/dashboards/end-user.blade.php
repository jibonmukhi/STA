@extends('layouts.advanced-dashboard')

@section('page-title', 'My Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body">
                    <h3 class="card-title mb-1">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="card-text opacity-75">Your personal dashboard - View your profile and company information</p>
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
                            <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">My Companies</h6>
                            <h4 class="mb-0 text-success">{{ $stats['my_companies'] }}</h4>
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
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Ownership</h6>
                            <h4 class="mb-0 text-info">{{ $stats['total_percentage'] }}%</h4>
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
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Profile Completion</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['profile_completion'] }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile & Company Information -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <!-- My Companies -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">My Companies</h5>
                    @if($primaryCompany)
                        <span class="badge bg-success">Primary: {{ $primaryCompany->name }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($userCompanies->count() > 0)
                        <div class="row">
                            @foreach($userCompanies as $company)
                                <div class="col-md-6 mb-3">
                                    <div class="card border {{ $company->pivot->is_primary ? 'border-success' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                                     class="avatar avatar-md rounded me-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ $company->name }}
                                                        @if($company->pivot->is_primary)
                                                            <i class="fas fa-star text-warning ms-1" title="Primary Company"></i>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted small mb-0">{{ $company->email }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="row text-center">
                                                    <div class="col">
                                                        <div class="text-muted small">Role</div>
                                                        <div class="fw-bold">{{ $company->pivot->role_in_company ?? 'Member' }}</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-muted small">Ownership</div>
                                                        <div class="fw-bold">{{ $company->pivot->percentage ?? 0 }}%</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-muted small">Joined</div>
                                                        <div class="fw-bold">{{ $company->pivot->joined_at ? \Carbon\Carbon::parse($company->pivot->joined_at)->format('M Y') : 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($company->pivot->percentage > 0)
                                                <div class="mt-2">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success"
                                                             style="width: {{ min($company->pivot->percentage, 100) }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($stats['total_percentage'] != 100)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Your total ownership percentage is {{ $stats['total_percentage'] }}%.
                                Please contact your company administrator to verify your allocations.
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building text-muted" style="font-size: 3rem;"></i>
                            <h6 class="mt-3 text-muted">No companies assigned</h6>
                            <p class="text-muted">Contact your system administrator to assign you to companies</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <!-- Profile Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Summary</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->name }}"
                             class="avatar avatar-xl rounded-circle">
                        <h5 class="mt-2 mb-0">{{ Auth::user()->full_name }}</h5>
                        <p class="text-muted">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="profile-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-{{ Auth::user()->status === 'active' ? 'success' : (Auth::user()->status === 'parked' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(Auth::user()->status) }}
                            </span>
                        </div>

                        @if(Auth::user()->phone)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phone:</span>
                                <span>{{ Auth::user()->phone }}</span>
                            </div>
                        @endif

                        @if(Auth::user()->date_of_birth)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Age:</span>
                                <span>{{ Auth::user()->age }} years</span>
                            </div>
                        @endif

                        @if(Auth::user()->country)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Country:</span>
                                <span>{{ Auth::user()->country }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Member Since:</span>
                            <span>{{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>

                        <!-- Profile Completion -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Profile Completion</span>
                                <span class="text-muted small">{{ $stats['profile_completion'] }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar"
                                     style="width: {{ $stats['profile_completion'] }}%"
                                     class="bg-{{ $stats['profile_completion'] >= 80 ? 'success' : ($stats['profile_completion'] >= 50 ? 'warning' : 'danger') }}"></div>
                            </div>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-1"></i>
                            Edit Profile
                        </a>
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
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-edit fa-2x mb-2 d-block"></i>
                                Update Profile
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                View Companies
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-chart-pie fa-2x mb-2 d-block"></i>
                                My Reports
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fas fa-cog fa-2x mb-2 d-block"></i>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection