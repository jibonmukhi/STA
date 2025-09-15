@extends('layouts.advanced-dashboard')

@section('page-title', 'Company Management')

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
    const hasAdvancedValues = {{ request('search_status') || request('search_date_from') || request('search_date_to') ? 'true' : 'false' }};
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
    const searchForm = document.querySelector('form[action*="companies"]');
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

<!-- Search Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card search-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>Search Companies
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('companies.index') }}">
                    <div class="row g-3">
                        <!-- ATECO Code Search - Priority -->
                        <div class="col-md-3">
                            <label for="search_ateco_code" class="form-label fw-semibold text-primary">
                                <i class="fas fa-tag me-1"></i>ATECO Code
                            </label>
                            <input type="text" class="form-control border-primary" id="search_ateco_code" name="search_ateco_code" 
                                   placeholder="Search by ATECO code..." value="{{ request('search_ateco_code') }}">
                        </div>
                        
                        <!-- Name Search -->
                        <div class="col-md-3">
                            <label for="search_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="search_name" name="search_name" 
                                   placeholder="Search by name..." value="{{ request('search_name') }}">
                        </div>
                        
                        <!-- Email Search -->
                        <div class="col-md-3">
                            <label for="search_email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="search_email" name="search_email" 
                                   placeholder="Search by email..." value="{{ request('search_email') }}">
                        </div>
                        
                        <!-- Phone Search -->
                        <div class="col-md-3">
                            <label for="search_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="search_phone" name="search_phone" 
                                   placeholder="Search by phone..." value="{{ request('search_phone') }}">
                        </div>
                    </div>
                    
                    <!-- P.IVA Search Row -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="search_piva" class="form-label">P.IVA / VAT</label>
                            <input type="text" class="form-control" id="search_piva" name="search_piva" 
                                   placeholder="Search by P.IVA..." value="{{ request('search_piva') }}">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                                @if(request('search_name') || request('search_email') || request('search_phone') || request('search_piva') || request('search_ateco_code'))
                                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
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
                        <div class="col-md-4">
                            <label for="search_status" class="form-label">Status</label>
                            <select class="form-select" id="search_status" name="search_status">
                                <option value="">All Status</option>
                                <option value="1" {{ request('search_status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('search_status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search_date_from" class="form-label">Created From</label>
                            <input type="date" class="form-control" id="search_date_from" name="search_date_from" 
                                   value="{{ request('search_date_from') }}">
                        </div>
                        <div class="col-md-4">
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
                    $hasAnySearch = request('search_name') || request('search_email') || request('search_phone') || request('search_piva') || request('search_ateco_code') || request('search_status') || request('search_date_from') || request('search_date_to');
                    $searchTerms = collect([
                        'Name' => request('search_name'),
                        'Email' => request('search_email'), 
                        'Phone' => request('search_phone'),
                        'P.IVA' => request('search_piva'),
                        'ATECO' => request('search_ateco_code')
                    ])->filter()->map(function($value, $key) {
                        return $key . ': "' . $value . '"';
                    })->join(', ');
                @endphp
                
                @if($hasAnySearch)
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Search Results
                        <small class="text-muted">({{ $companies->total() }} found)</small>
                    </h5>
                    @if($searchTerms)
                        <p class="mb-0 mt-1 text-muted small">
                            <i class="fas fa-filter me-1"></i>Filtered by: {{ $searchTerms }}
                            @if(request('search_status'))
                                , Status: {{ request('search_status') == '1' ? 'Active' : 'Inactive' }}
                            @endif
                            @if(request('search_date_from') || request('search_date_to'))
                                , Date: {{ request('search_date_from') ?? 'Any' }} to {{ request('search_date_to') ?? 'Any' }}
                            @endif
                        </p>
                    @endif
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        All Companies
                        <small class="text-muted">({{ $companies->total() }} total)</small>
                    </h5>
                @endif
            </div>
            
            @can('create companies')
            <a href="{{ route('companies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Company
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
                    <i class="fas fa-building me-2"></i>All Companies
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="8%" class="bg-light">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'ateco_code', 'direction' => request('sort') === 'ateco_code' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-primary fw-semibold">
                                        <i class="fas fa-tag me-1"></i>ATECO
                                        @if(request('sort') === 'ateco_code')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="8%">Logo</th>
                                <th width="15%">
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
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'phone', 'direction' => request('sort') === 'phone' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Phone
                                        @if(request('sort') === 'phone')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'piva', 'direction' => request('sort') === 'piva' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        P.IVA
                                        @if(request('sort') === 'piva')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                
                                <th width="8%">Website</th>
                                <th width="8%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'active', 'direction' => request('sort') === 'active' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Status
                                        @if(request('sort') === 'active')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
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
                                <th width="11%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                            <tr>
                                <td>{{ $company->id }}</td>
                                <td>
                                    @if($company->ateco_code)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-tag me-1"></i>{{ $company->ateco_code }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" 
                                             class="rounded shadow-sm" width="80" height="80" style="object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $company->name }}</div>
                                            @if($company->address)
                                                <small class="text-muted">{{ Str::limit($company->address, 30) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($company->email)
                                        <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                            {{ $company->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($company->phone)
                                        <a href="tel:{{ $company->phone }}" class="text-decoration-none">
                                            {{ $company->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $company->piva ?: '-' }}
                                </td>
                                
                                <td>
                                    @if($company->website)
                                        <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($company->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $company->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('view companies')
                                        <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit companies')
                                        <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete companies')
                                        <form method="POST" action="{{ route('companies.destroy', $company) }}" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this company?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No companies found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Per Page Controls -->
                @if($companies->hasPages() || $companies->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">Show:</label>
                        <form method="GET" action="{{ route('companies.index') }}" class="d-flex align-items-center">
                            <!-- Preserve search parameters -->
                            @if(request('search_name'))
                                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                            @endif
                            @if(request('search_email'))
                                <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                            @endif
                            @if(request('search_phone'))
                                <input type="hidden" name="search_phone" value="{{ request('search_phone') }}">
                            @endif
                            @if(request('search_piva'))
                                <input type="hidden" name="search_piva" value="{{ request('search_piva') }}">
                            @endif
                            @if(request('search_status'))
                                <input type="hidden" name="search_status" value="{{ request('search_status') }}">
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
                    @if($companies->hasPages())
                    <div>
                        {{ $companies->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <div>
                        Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }} 
                        of {{ $companies->total() }} results
                        @if(request('search'))
                            for "{{ request('search') }}"
                        @endif
                    </div>
                    <div>
                        Page {{ $companies->currentPage() }} of {{ $companies->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
