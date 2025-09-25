@extends('layouts.advanced-dashboard')

@section('page-title', trans('companies.my_companies'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ trans('companies.my_companies') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ trans('companies.manage_company_affiliations') }}</p>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50 small">{{ trans('companies.total_companies') }}</div>
                            <div class="h2 mb-0">{{ $companies->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($companies->count() > 0)
        <!-- Companies Grid -->
        <div class="row">
            @foreach($companies as $company)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 {{ $company->pivot->is_primary ? 'border-success' : '' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                     class="avatar avatar-sm rounded me-2">
                                <h6 class="card-title mb-0">{{ $company->name }}</h6>
                            </div>
                            @if($company->pivot->is_primary)
                                <span class="badge bg-success">{{ trans('companies.primary') }}</span>
                            @endif
                        </div>

                        <div class="card-body">
                            <!-- Company Information -->
                            <div class="company-info mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border-end">
                                            <div class="h5 mb-0 text-primary">{{ $company->users->count() }}</div>
                                            <small class="text-muted">{{ trans('companies.users') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-end">
                                            <div class="h5 mb-0 text-success">{{ $company->pivot->percentage ?? 0 }}%</div>
                                            <small class="text-muted">{{ trans('companies.ownership') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="h5 mb-0 text-info">
                                            <i class="fas fa-{{ $company->active ? 'check-circle' : 'times-circle' }}"></i>
                                        </div>
                                        <small class="text-muted">{{ $company->active ? trans('companies.active') : trans('companies.inactive') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Details -->
                            <div class="company-details">
                                @if($company->email)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope text-muted me-2" style="width: 16px;"></i>
                                        <small class="text-muted">{{ $company->email }}</small>
                                    </div>
                                @endif

                                @if($company->phone)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-phone text-muted me-2" style="width: 16px;"></i>
                                        <small class="text-muted">{{ $company->phone }}</small>
                                    </div>
                                @endif

                                @if($company->website)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-globe text-muted me-2" style="width: 16px;"></i>
                                        <small class="text-muted">
                                            <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                                {{ $company->website }}
                                            </a>
                                        </small>
                                    </div>
                                @endif

                                @if($company->pivot->role_in_company)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-tag text-muted me-2" style="width: 16px;"></i>
                                        <small class="text-muted">{{ trans('companies.role') }}: <strong>{{ $company->pivot->role_in_company }}</strong></small>
                                    </div>
                                @endif

                                @if($company->pivot->joined_at)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-muted me-2" style="width: 16px;"></i>
                                        <small class="text-muted">{{ trans('companies.joined') }}: {{ \Carbon\Carbon::parse($company->pivot->joined_at)->format('M d, Y') }}</small>
                                    </div>
                                @endif
                            </div>

                            <!-- Ownership Progress Bar -->
                            @if($company->pivot->percentage > 0)
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">{{ trans('companies.ownership_percentage') }}</small>
                                        <small class="fw-bold">{{ $company->pivot->percentage }}%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ min($company->pivot->percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewCompanyDetails({{ $company->id }})">
                                    <i class="fas fa-eye me-1"></i> {{ trans('companies.view_details') }}
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="manageCompanyUsers({{ $company->id }})">
                                    <i class="fas fa-users me-1"></i> {{ trans('companies.manage_users') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ trans('companies.company_overview_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center">
                                    <div class="h3 text-primary mb-1">{{ $companies->count() }}</div>
                                    <div class="text-muted">{{ trans('companies.total_companies') }}</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center">
                                    <div class="h3 text-success mb-1">{{ $companies->where('active', true)->count() }}</div>
                                    <div class="text-muted">{{ trans('companies.active_companies') }}</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center">
                                    <div class="h3 text-info mb-1">{{ $companies->sum(function($c) { return $c->users->count(); }) }}</div>
                                    <div class="text-muted">{{ trans('companies.total_users') }}</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center">
                                    <div class="h3 text-warning mb-1">{{ $companies->sum('pivot.percentage') }}%</div>
                                    <div class="text-muted">{{ trans('companies.total_ownership') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Users Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ trans('companies.all_company_users') }}</h5>
                        <a href="{{ route('company-users.index') }}" class="btn btn-primary">
                            <i class="fas fa-users me-1"></i> {{ trans('companies.manage_all_users') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $allUsers = collect();
                            foreach($companies as $company) {
                                foreach($company->users as $user) {
                                    $user->company_name = $company->name;
                                    $allUsers->push($user);
                                }
                            }
                            $allUsers = $allUsers->unique('id');
                        @endphp

                        @if($allUsers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('companies.user') }}</th>
                                            <th>{{ trans('companies.email') }}</th>
                                            <th>{{ trans('companies.company') }}</th>
                                            <th>{{ trans('companies.role') }}</th>
                                            <th>{{ trans('companies.status') }}</th>
                                            <th>{{ trans('companies.joined') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allUsers->take(10) as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                                         class="avatar avatar-sm rounded-circle me-2">
                                                    <div>
                                                        <div class="fw-bold">{{ $user->full_name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->company_name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $user->companies->first()->pivot->role_in_company ?? trans('companies.member') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'parked' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($allUsers->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('company-users.index') }}" class="btn btn-outline-primary">
                                        {{ trans('companies.view_all_users', ['count' => $allUsers->count()]) }}
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                                <h6 class="mt-3 text-muted">{{ trans('companies.no_users_found') }}</h6>
                                <p class="text-muted">{{ trans('companies.no_users_assigned_companies') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-building text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-4 text-muted">{{ trans('companies.no_companies_assigned') }}</h4>
                        <p class="text-muted mb-4">{{ trans('companies.not_assigned_companies_message') }}</p>
                        <a href="{{ route('company.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> {{ trans('companies.back_to_dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function viewCompanyDetails(companyId) {
        // In a real implementation, this would show a modal or navigate to company details
        alert(`Viewing details for company ID: ${companyId}\nThis would show detailed company information.`);
    }

    function manageCompanyUsers(companyId) {
        // In a real implementation, this would filter users by company
        window.location.href = "{{ route('company-users.index') }}?company=" + companyId;
    }
</script>
@endsection