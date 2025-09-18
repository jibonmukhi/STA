@extends('layouts.advanced-dashboard')

@section('page-title', 'User Management')

@push('styles')
<style>
.table th a {
    color: inherit;
    text-decoration: none;
}
.table th a:hover {
    color: var(--primary-color);
}
.table .sorting-icon {
    opacity: 0.5;
    transition: opacity 0.2s;
}
.table th:hover .sorting-icon {
    opacity: 1;
}
.search-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
}
.pagination-controls {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}
.user-parked {
    background-color: #fff3cd !important;
    border-left: 4px solid #ffc107 !important;
}
.approval-needed {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
// Advanced search toggle
function toggleAdvancedSearch() {
    const advancedDiv = document.getElementById('advanced-search');
    const toggleBtn = document.querySelector('button[onclick="toggleAdvancedSearch()"]');
    
    if (advancedDiv.style.display === 'none' || advancedDiv.style.display === '') {
        advancedDiv.style.display = 'flex';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>Hide Advanced';
        toggleBtn.classList.remove('btn-outline-info');
        toggleBtn.classList.add('btn-info');
    } else {
        advancedDiv.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>Advanced Options';
        toggleBtn.classList.remove('btn-info');
        toggleBtn.classList.add('btn-outline-info');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Show advanced search if any advanced fields have values
    const hasAdvancedValues = {{ request('search_gender') || request('search_status') || request('search_company') || request('search_date_from') || request('search_date_to') ? 'true' : 'false' }};
    if (hasAdvancedValues) {
        toggleAdvancedSearch();
    }
    
    // Auto-submit search forms on Enter key
    const searchInputs = document.querySelectorAll('input[name^="search_"]');
    searchInputs.forEach(function(input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.closest('form').submit();
            }
        });
    });
    
    // Add loading state to search button
    const searchForm = document.querySelector('form[action*="users"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Searching...';
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds in case of issues
                setTimeout(function() {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    }
    
    // Clear individual fields with Escape key
    searchInputs.forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.focus();
            }
        });
    });
});
</script>
@endpush

@section('content')
<!-- Display Flash Messages -->
@include('components.flash-messages')

<!-- Pending Approvals Alert -->
@php
    $pendingUsers = $users->filter(fn($user) => $user->status === 'parked');
    $pendingCount = $pendingUsers->count();
@endphp

@if($pendingCount > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">Users Pending Approval</h6>
                <p class="mb-0">
                    <strong>{{ $pendingCount }}</strong> {{ $pendingCount === 1 ? 'user needs' : 'users need' }} admin approval to become active.
                    <a href="{{ route('users.index', ['search_status' => 'parked']) }}" class="alert-link ms-2">
                        <i class="fas fa-filter me-1"></i>View Pending Users
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card search-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>Search Users
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row g-3">
                        <!-- Name Search -->
                        <div class="col-md-3">
                            <label for="search_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="search_name" name="search_name" 
                                   placeholder="Search by name..." value="{{ request('search_name') }}">
                        </div>
                        
                        <!-- Surname Search -->
                        <div class="col-md-3">
                            <label for="search_surname" class="form-label">Surname</label>
                            <input type="text" class="form-control" id="search_surname" name="search_surname" 
                                   placeholder="Search by surname..." value="{{ request('search_surname') }}">
                        </div>
                        
                        <!-- Email Search -->
                        <div class="col-md-3">
                            <label for="search_email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="search_email" name="search_email" 
                                   placeholder="Search by email..." value="{{ request('search_email') }}">
                        </div>
                        
                        <!-- Mobile Search -->
                        <div class="col-md-3">
                            <label for="search_mobile" class="form-label">Mobile</label>
                            <input type="text" class="form-control" id="search_mobile" name="search_mobile" 
                                   placeholder="Search by mobile..." value="{{ request('search_mobile') }}">
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <!-- Tax ID Search -->
                        <div class="col-md-3">
                            <label for="search_tax_id" class="form-label">Tax ID Code</label>
                            <input type="text" class="form-control" id="search_tax_id" name="search_tax_id" 
                                   placeholder="Search by tax ID..." value="{{ request('search_tax_id') }}">
                        </div>
                        <div class="col-md-9"></div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                                @if(request('search_name') || request('search_surname') || request('search_email') || request('search_mobile') || request('search_tax_id'))
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear All
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-info ms-auto" onclick="toggleAdvancedSearch()">
                                    <i class="fas fa-cog me-1"></i>Advanced Options
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Search Options (Hidden by default) -->
                    <div id="advanced-search" class="row mt-3" style="display: none;">
                        <div class="col-md-3">
                            <label for="search_gender" class="form-label">Gender</label>
                            <select class="form-select" id="search_gender" name="search_gender">
                                <option value="">All Genders</option>
                                <option value="male" {{ request('search_gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('search_gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ request('search_gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_status" class="form-label">Status</label>
                            <select class="form-select" id="search_status" name="search_status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('search_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="parked" {{ request('search_status') == 'parked' ? 'selected' : '' }}>Parked (Pending Approval)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_company" class="form-label">Company</label>
                            <select class="form-select" id="search_company" name="search_company">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('search_company') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3 mt-3">
                            <label for="search_date_from" class="form-label">Created From</label>
                            <input type="date" class="form-control" id="search_date_from" name="search_date_from" 
                                   value="{{ request('search_date_from') }}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="search_date_to" class="form-label">Created To</label>
                            <input type="date" class="form-control" id="search_date_to" name="search_date_to" 
                                   value="{{ request('search_date_to') }}">
                        </div>
                    </div>
                    
                    <!-- Preserve other parameters -->
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Actions Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @php
                    $hasAnySearch = request('search_name') || request('search_surname') || request('search_email') || request('search_mobile') || request('search_tax_id') || request('search_gender') || request('search_status') || request('search_company') || request('search_date_from') || request('search_date_to');
                    $searchTerms = collect([
                        'Name' => request('search_name'),
                        'Surname' => request('search_surname'),
                        'Email' => request('search_email'), 
                        'Mobile' => request('search_mobile'),
                        'Tax ID' => request('search_tax_id')
                    ])->filter()->map(function($value, $key) {
                        return $key . ': "' . $value . '"';
                    })->join(', ');
                @endphp
                
                @if($hasAnySearch)
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Search Results
                        <small class="text-muted">({{ $users->total() }} found)</small>
                    </h5>
                    @if($searchTerms)
                        <p class="mb-0 mt-1 text-muted small">
                            <i class="fas fa-filter me-1"></i>Filtered by: {{ $searchTerms }}
                            @if(request('search_gender'))
                                , Gender: {{ ucfirst(request('search_gender')) }}
                            @endif
                            @if(request('search_status'))
                                , Status: {{ request('search_status') == '1' ? 'Active' : 'Inactive' }}
                            @endif
                            @if(request('search_company'))
                                , Company: {{ $companies->find(request('search_company'))->name ?? 'Unknown' }}
                            @endif
                            @if(request('search_date_from') || request('search_date_to'))
                                , Date: {{ request('search_date_from') ?? 'Any' }} to {{ request('search_date_to') ?? 'Any' }}
                            @endif
                        </p>
                    @endif
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        All Users
                        <small class="text-muted">({{ $users->total() }} total)</small>
                    </h5>
                @endif
            </div>
            
            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New User
            </a>
            @endcan
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>All Users
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Name
                                        @if(request('sort') === 'name')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Email
                                        @if(request('sort') === 'email')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'mobile', 'direction' => request('sort') === 'mobile' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Mobile
                                        @if(request('sort') === 'mobile')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="8%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Status
                                        @if(request('sort') === 'status')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="12%">Primary Company</th>
                                <th width="8%">Roles</th>
                                <th width="10%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Created At
                                        @if(request('sort') === 'created_at')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="{{ $user->status === 'parked' ? 'user-parked' : '' }}">
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                        <div>
                                            <div class="fw-bold">{{ $user->full_name }}</div>
                                            @if($user->id === auth()->id())
                                                <small class="text-muted">(You)</small>
                                            @endif
                                            @if($user->age)
                                                <small class="text-muted">Age: {{ $user->age }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($user->mobile)
                                        <a href="tel:{{ $user->mobile }}" class="text-decoration-none">
                                            {{ $user->mobile }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($user->status === 'parked')
                                        <span class="badge bg-warning approval-needed">
                                            <i class="fas fa-clock me-1"></i>Pending Approval
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $primaryCompany = $user->companies->where('pivot.is_primary', true)->first();
                                    @endphp
                                    @if($primaryCompany)
                                        <span class="badge bg-primary">{{ $primaryCompany->name }}</span>
                                    @elseif($user->companies->count() > 0)
                                        <span class="badge bg-secondary">{{ $user->companies->first()->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->roles->count() > 0)
                                        <span class="badge bg-info">{{ $user->formatted_role }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Role</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('view users')
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit users')
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete users')
                                        @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No users found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Per Page Controls -->
                @if($users->hasPages() || $users->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">Show:</label>
                        <form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center">
                            <!-- Preserve search parameters -->
                            @if(request('search_name'))
                                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                            @endif
                            @if(request('search_surname'))
                                <input type="hidden" name="search_surname" value="{{ request('search_surname') }}">
                            @endif
                            @if(request('search_email'))
                                <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                            @endif
                            @if(request('search_mobile'))
                                <input type="hidden" name="search_mobile" value="{{ request('search_mobile') }}">
                            @endif
                            @if(request('search_tax_id'))
                                <input type="hidden" name="search_tax_id" value="{{ request('search_tax_id') }}">
                            @endif
                            @if(request('search_gender'))
                                <input type="hidden" name="search_gender" value="{{ request('search_gender') }}">
                            @endif
                            @if(request('search_status'))
                                <input type="hidden" name="search_status" value="{{ request('search_status') }}">
                            @endif
                            @if(request('search_company'))
                                <input type="hidden" name="search_company" value="{{ request('search_company') }}">
                            @endif
                            @if(request('search_date_from'))
                                <input type="hidden" name="search_date_from" value="{{ request('search_date_from') }}">
                            @endif
                            @if(request('search_date_to'))
                                <input type="hidden" name="search_date_to" value="{{ request('search_date_to') }}">
                            @endif
                            <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
                            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                            
                            <select name="per_page" class="form-select form-select-sm me-2" style="width: auto;" onchange="this.form.submit()">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-muted small">entries per page</span>
                        </form>
                    </div>

                    <!-- Pagination (Right) -->
                    @if($users->hasPages())
                    <div>
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <div>
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                        of {{ $users->total() }} results
                    </div>
                    <div>
                        Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection