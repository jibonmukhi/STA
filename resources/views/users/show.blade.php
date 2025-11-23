@extends('layouts.advanced-dashboard')

@section('page-title', __('users.user_details'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-user me-2"></i>{{ $user->full_name }}
                @if($user->status)
                    <span class="badge bg-success ms-2">{{ __('users.active') }}</span>
                @else
                    <span class="badge bg-secondary ms-2">{{ __('users.inactive') }}</span>
                @endif
                @if($user->id === auth()->id())
                    <span class="badge bg-info ms-1">{{ __('users.you') }}</span>
                @endif
            </h4>
            <div class="btn-group">
                <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.index') : route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('users.back_to_users') }}
                </a>
                @can('edit users')
                <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.edit', $user) : route('users.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>{{ __('users.edit_user_btn') }}
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Display Flash Messages -->
@include('components.flash-messages')

<div class="row">
    <!-- Personal Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>{{ __('users.personal_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.first_name') }}</label>
                            <p class="fw-bold mb-1">{{ $user->name ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.surname') }}</label>
                            <p class="fw-bold mb-1">{{ $user->surname ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.gender') }}</label>
                            <p class="fw-bold mb-1">
                                @if($user->gender)
                                    <span class="badge bg-info">{{ ucfirst($user->gender) }}</span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.date_of_birth') }}</label>
                            <p class="fw-bold mb-1">
                                @if($user->date_of_birth)
                                    {{ $user->date_of_birth->format('F d, Y') }}
                                    <small class="text-muted d-block">{{ __('users.age') }}: {{ $user->age }} {{ __('users.years_old') }}</small>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.tax_id_code') }}</label>
                            <p class="fw-bold mb-1">{{ $user->cf ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-address-card me-2"></i>{{ __('users.contact_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.email_address') }}</label>
                            <p class="fw-bold mb-1">
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small">{{ __('users.mobile') }}</label>
                            <p class="fw-bold mb-1">
                                @if($user->mobile)
                                    <a href="tel:{{ $user->mobile }}" class="text-decoration-none">
                                        {{ $user->mobile }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($user->address)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Address</label>
                            <p class="fw-bold mb-1">{{ $user->address }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Company Associations -->
        @if($user->companies->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Associations
                    <span class="badge bg-primary ms-2">{{ $user->companies->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($user->companies as $company)
                    <div class="col-md-6 mb-3">
                        <div class="card border {{ $company->pivot->is_primary ? 'border-primary' : 'border-secondary' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-1">
                                        {{ $company->name }}
                                        @if($company->pivot->is_primary)
                                            <i class="fas fa-star text-warning ms-1" title="Primary Company"></i>
                                        @endif
                                    </h6>
                                    @if($company->pivot->is_primary)
                                        <span class="badge bg-primary">Primary</span>
                                    @else
                                        <span class="badge bg-secondary">Secondary</span>
                                    @endif
                                </div>
                                
                                @if($company->email)
                                    <p class="card-text small text-muted mb-1">
                                        <i class="fas fa-envelope me-1"></i>
                                        <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                            {{ $company->email }}
                                        </a>
                                    </p>
                                @endif
                                
                                @if($company->phone)
                                    <p class="card-text small text-muted mb-1">
                                        <i class="fas fa-phone me-1"></i>
                                        <a href="tel:{{ $company->phone }}" class="text-decoration-none">
                                            {{ $company->phone }}
                                        </a>
                                    </p>
                                @endif
                                
                                @if($company->pivot->role_in_company)
                                    <p class="card-text small mb-1">
                                        <strong>Role:</strong> {{ $company->pivot->role_in_company }}
                                    </p>
                                @endif
                                
                                @if($company->pivot->joined_at)
                                    <p class="card-text small text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        Joined: {{ \Carbon\Carbon::parse($company->pivot->joined_at)->format('M d, Y') }}
                                    </p>
                                @endif
                                
                                <div class="mt-2">
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View Company
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- User Profile Photo -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-camera me-2"></i>{{ __('users.profile_photo') ?? 'Profile Photo' }}
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $user->photo_url }}" alt="{{ $user->full_name }}"
                     class="img-fluid rounded-circle shadow-sm mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h6 class="mb-1">{{ $user->full_name }}</h6>
                <p class="text-muted small mb-0">{{ $user->email }}</p>
            </div>
        </div>

        <!-- System Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">User ID</label>
                    <p class="fw-bold mb-1">#{{ $user->id }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted small">Status</label>
                    <p class="mb-1">
                        @if($user->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Created</label>
                    <p class="fw-bold mb-1">{{ $user->created_at->format('M d, Y') }}</p>
                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                </div>

                <div class="mb-0">
                    <label class="form-label text-muted small">Last Updated</label>
                    <p class="fw-bold mb-1">{{ $user->updated_at->format('M d, Y') }}</p>
                    <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-shield me-2"></i>Roles & Permissions
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Assigned Roles</label>
                    <div>
                        @if($user->roles->count() > 0)
                            <span class="badge bg-primary">{{ $user->formatted_role }}</span>
                        @else
                            <span class="badge bg-secondary">No Role Assigned</span>
                        @endif
                    </div>
                </div>

                @if($user->getAllPermissions()->count() > 0)
                <div class="mb-0">
                    <label class="form-label text-muted small">Permissions</label>
                    <div class="small">
                        @foreach($user->getAllPermissions() as $permission)
                            <span class="badge bg-success me-1 mb-1">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($user->companies->count() == 0)
        <!-- No Companies Notice -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Associations
                </h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">No company associations found.</p>
                @can('edit users')
                    <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.edit', $user) : route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Companies
                    </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('edit users')
                    <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.edit', $user) : route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    @endcan
                    
                    @can('delete users')
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                    @endif
                    @endcan

                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-h me-2"></i>More Actions
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item" href="mailto:{{ $user->email }}">
                                <i class="fas fa-envelope me-2"></i>Send Email
                            </a></li>
                            @if($user->mobile)
                            <li><a class="dropdown-item" href="tel:{{ $user->mobile }}">
                                <i class="fas fa-phone me-2"></i>Call User
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('users.index', ['search_email' => $user->email]) }}">
                                <i class="fas fa-search me-2"></i>Find Similar Users
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection