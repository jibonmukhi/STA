@extends('layouts.advanced-dashboard')

@section('page-title', __('roles.role_management'))

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
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('roles.hide_advanced') }}';
        toggleBtn.classList.remove('btn-outline-info');
        toggleBtn.classList.add('btn-info');
    } else {
        advancedDiv.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('roles.advanced_options') }}';
        toggleBtn.classList.remove('btn-info');
        toggleBtn.classList.add('btn-outline-info');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Show advanced search if any advanced fields have values
    const hasAdvancedValues = {{ request('search_permission') || request('search_users_count') || request('search_date_from') || request('search_date_to') ? 'true' : 'false' }};
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
    const searchForm = document.querySelector('form[action*="roles"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __('roles.searching') }}';
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
                    <i class="fas fa-search me-2"></i>{{ __('roles.search_roles') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('roles.index') }}">
                    <div class="row g-3">
                        <!-- Role Name Search -->
                        <div class="col-md-6">
                            <label for="search_name" class="form-label">{{ __('roles.role_name') }}</label>
                            <input type="text" class="form-control" id="search_name" name="search_name" 
                                   placeholder="{{ __('roles.search_by_role_name') }}" value="{{ request('search_name') }}">
                        </div>
                        
                        <!-- Permission Search -->
                        <div class="col-md-6">
                            <label for="search_permission" class="form-label">{{ __('roles.permission') }}</label>
                            <input type="text" class="form-control" id="search_permission" name="search_permission" 
                                   placeholder="{{ __('roles.search_by_permission_name') }}" value="{{ request('search_permission') }}">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>{{ __('roles.search') }}
                                </button>
                                @if(request('search_name') || request('search_permission'))
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>{{ __('roles.clear_all') }}
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-info ms-auto" onclick="toggleAdvancedSearch()">
                                    <i class="fas fa-cog me-1"></i>{{ __('roles.advanced_options') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Search Options (Hidden by default) -->
                    <div id="advanced-search" class="row mt-3" style="display: none;">
                        <div class="col-md-4">
                            <label for="search_users_count" class="form-label">{{ __('roles.users_count') }}</label>
                            <input type="number" class="form-control" id="search_users_count" name="search_users_count" 
                                   placeholder="{{ __('roles.filter_by_users_count') }}" value="{{ request('search_users_count') }}" min="0">
                        </div>
                        <div class="col-md-4">
                            <label for="search_date_from" class="form-label">{{ __('roles.created_from') }}</label>
                            <input type="date" class="form-control" id="search_date_from" name="search_date_from" 
                                   value="{{ request('search_date_from') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="search_date_to" class="form-label">{{ __('roles.created_to') }}</label>
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
                    $hasAnySearch = request('search_name') || request('search_permission') || request('search_users_count') || request('search_date_from') || request('search_date_to');
                    $searchTerms = collect([
                        __('roles.name') => request('search_name'),
                        __('roles.permission') => request('search_permission')
                    ])->filter()->map(function($value, $key) {
                        return $key . ': "' . $value . '"';
                    })->join(', ');
                @endphp
                
                @if($hasAnySearch)
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        {{ __('roles.search_results') }}
                        <small class="text-muted">({{ $roles->total() }} {{ __('roles.found') }})</small>
                    </h5>
                    @if($searchTerms)
                        <p class="mb-0 mt-1 text-muted small">
                            <i class="fas fa-filter me-1"></i>{{ __('roles.filtered_by') }}: {{ $searchTerms }}
                            @if(request('search_users_count'))
                                , {{ __('roles.users_count') }}: {{ request('search_users_count') }}
                            @endif
                            @if(request('search_date_from') || request('search_date_to'))
                                , {{ __('roles.date') }}: {{ request('search_date_from') ?? __('roles.any') }} {{ __('roles.to') }} {{ request('search_date_to') ?? __('roles.any') }}
                            @endif
                        </p>
                    @endif
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        {{ __('roles.all_roles') }}
                        <small class="text-muted">({{ $roles->total() }} {{ __('roles.total') }})</small>
                    </h5>
                @endif
            </div>
            
            @can('create roles')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('roles.add_new_role') }}
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
                    <i class="fas fa-user-shield me-2"></i>{{ __('roles.all_roles') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ __('roles.role_name_column') }}
                                        @if(request('sort') === 'name')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="35%">{{ __('roles.permissions_column') }}</th>
                                <th width="10%">{{ __('roles.users_count_column') }}</th>
                                <th width="15%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ __('roles.created_at_column') }}
                                        @if(request('sort') === 'created_at')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="10%">{{ __('roles.actions_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-shield fa-lg me-2 text-primary"></i>
                                        <div>
                                            <div class="fw-bold">{{ $role->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($role->permissions->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-success">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} {{ __('roles.more') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">{{ __('roles.no_permissions') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $role->users()->count() }}</span>
                                </td>
                                <td>{{ $role->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('view roles')
                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="{{ __('roles.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit roles')
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="{{ __('roles.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete roles')
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" style="display: inline;" 
                                              onsubmit="return confirm('{{ __('roles.delete_role_confirmation') }}')"
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('roles.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('roles.no_roles_found_message') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Per Page Controls -->
                @if($roles->hasPages() || $roles->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">{{ __('roles.show') }}:</label>
                        <form method="GET" action="{{ route('roles.index') }}" class="d-flex align-items-center">
                            <!-- Preserve search parameters -->
                            @if(request('search_name'))
                                <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                            @endif
                            @if(request('search_permission'))
                                <input type="hidden" name="search_permission" value="{{ request('search_permission') }}">
                            @endif
                            @if(request('search_users_count'))
                                <input type="hidden" name="search_users_count" value="{{ request('search_users_count') }}">
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
                            <span class="text-muted small">{{ __('roles.entries_per_page') }}</span>
                        </form>
                    </div>

                    <!-- Pagination (Right) -->
                    @if($roles->hasPages())
                    <div>
                        {{ $roles->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <div>
                        {{ __('roles.showing') }} {{ $roles->firstItem() ?? 0 }} {{ __('roles.to') }} {{ $roles->lastItem() ?? 0 }}
                        {{ __('roles.of') }} {{ $roles->total() }} {{ __('roles.results') }}
                    </div>
                    <div>
                        {{ __('roles.page') }} {{ $roles->currentPage() }} {{ __('roles.of') }} {{ $roles->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection