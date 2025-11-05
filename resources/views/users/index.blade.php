@extends('layouts.advanced-dashboard')

@section('page-title', __('users.user_management'))

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
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('users.hide_advanced') }}';
        toggleBtn.classList.remove('btn-outline-info');
        toggleBtn.classList.add('btn-info');
    } else {
        advancedDiv.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('users.advanced_options') }}';
        toggleBtn.classList.remove('btn-info');
        toggleBtn.classList.add('btn-outline-info');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Show advanced search if any advanced fields have values
    const hasAdvancedValues = {{ request('search_mobile') || request('search_gender') || request('search_status') || request('search_date_from') || request('search_date_to') ? 'true' : 'false' }};
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __('users.searching') }}';
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

    const bulkForm = document.getElementById('bulk-status-form');
    if (bulkForm) {
        const selectAllCheckbox = document.getElementById('select-all-users');
        const userCheckboxes = Array.from(document.querySelectorAll('.user-select-checkbox'));
        const statusSelect = document.getElementById('bulk-status-select');
        const applyButton = document.getElementById('bulk-status-apply');
        const noneSelectedMessage = '{{ __('users.bulk_status_none_selected') }}';
        const noStatusMessage = '{{ __('users.bulk_status_select_status') }}';

        const updateButtonState = () => {
            if (!applyButton) {
                return;
            }

            const selectedCount = userCheckboxes.filter(cb => cb.checked).length;
            const hasStatus = !!statusSelect.value;
            applyButton.disabled = !(selectedCount > 0 && hasStatus);
        };

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', () => {
                userCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateButtonState();
            });
        }

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (!cb.checked && selectAllCheckbox?.checked) {
                    selectAllCheckbox.checked = false;
                }
                updateButtonState();
            });
        });

        statusSelect?.addEventListener('change', updateButtonState);

        bulkForm.addEventListener('submit', (event) => {
            const selectedCount = userCheckboxes.filter(cb => cb.checked).length;
            if (selectedCount === 0) {
                event.preventDefault();
                alert(noneSelectedMessage);
                return;
            }

            if (!statusSelect.value) {
                event.preventDefault();
                alert(noStatusMessage);
                return;
            }
        });

        updateButtonState();
    }
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
    $isStaManager = auth()->user()?->hasRole('sta_manager');
@endphp

@if($pendingCount > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">{{ __('users.users_pending_approval') }}</h6>
                <p class="mb-0">
                    {{ __('users.users_need_approval', ['count' => $pendingCount, 'users' => $pendingCount === 1 ? __('users.user_needs') : __('users.users_need')]) }}
                    <a href="{{ route('users.index', ['search_status' => 'parked']) }}" class="alert-link ms-2">
                        <i class="fas fa-filter me-1"></i>{{ __('users.view_pending_users') }}
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
                    <i class="fas fa-search me-2"></i>{{ __('users.search_users') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row g-3">
                        <!-- Name Search -->
                        <div class="col-md-3">
                            <label for="search_name" class="form-label">{{ __('users.first_name') }}</label>
                            <input type="text" class="form-control" id="search_name" name="search_name"
                                   placeholder="{{ __('users.search_by_name') }}" value="{{ request('search_name') }}">
                        </div>

                        <!-- Surname Search -->
                        <div class="col-md-3">
                            <label for="search_surname" class="form-label">{{ __('users.surname') }}</label>
                            <input type="text" class="form-control" id="search_surname" name="search_surname"
                                   placeholder="{{ __('users.search_by_surname') }}" value="{{ request('search_surname') }}">
                        </div>

                        <!-- Email Search -->
                        <div class="col-md-3">
                            <label for="search_email" class="form-label">{{ __('users.email') }}</label>
                            <input type="text" class="form-control" id="search_email" name="search_email"
                                   placeholder="{{ __('users.search_by_email') }}" value="{{ request('search_email') }}">
                        </div>

                        <!-- Company Search -->
                        <div class="col-md-3">
                            <label for="search_company" class="form-label">{{ __('users.company') }}</label>
                            <select class="form-select" id="search_company" name="search_company">
                                <option value="">{{ __('users.all_companies') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('search_company') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <!-- Tax ID Search -->
                        <div class="col-md-3">
                            <label for="search_tax_id" class="form-label">{{ __('users.tax_id_code') }}</label>
                            <input type="text" class="form-control" id="search_tax_id" name="search_tax_id" 
                                   placeholder="{{ __('users.search_by_tax_id') }}" value="{{ request('search_tax_id') }}">
                        </div>
                        <div class="col-md-9"></div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>{{ __('users.search') }}
                                </button>
                                @if(request('search_name') || request('search_surname') || request('search_email') || request('search_company') || request('search_tax_id') || request('search_mobile') || request('search_gender') || request('search_status') || request('search_date_from') || request('search_date_to'))
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>{{ __('users.clear_all') }}
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-info ms-auto" onclick="toggleAdvancedSearch()">
                                    <i class="fas fa-cog me-1"></i>{{ __('users.advanced_options') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Search Options (Hidden by default) -->
                    <div id="advanced-search" class="row mt-3" style="display: none;">
                        <div class="col-md-3">
                            <label for="search_mobile" class="form-label">{{ __('users.mobile') }}</label>
                            <input type="text" class="form-control" id="search_mobile" name="search_mobile"
                                   placeholder="{{ __('users.search_by_mobile') }}" value="{{ request('search_mobile') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="search_gender" class="form-label">{{ __('users.gender') }}</label>
                            <select class="form-select" id="search_gender" name="search_gender">
                                <option value="">{{ __('users.all_genders') }}</option>
                                <option value="male" {{ request('search_gender') == 'male' ? 'selected' : '' }}>{{ __('users.male') }}</option>
                                <option value="female" {{ request('search_gender') == 'female' ? 'selected' : '' }}>{{ __('users.female') }}</option>
                                <option value="other" {{ request('search_gender') == 'other' ? 'selected' : '' }}>{{ __('users.other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search_status" class="form-label">{{ __('users.status') }}</label>
                            <select class="form-select" id="search_status" name="search_status">
                                <option value="">{{ __('users.all_status') }}</option>
                                @foreach(dataVaultItems('user_status') as $item)
                                    <option value="{{ $item['code'] }}" {{ request('search_status') == $item['code'] ? 'selected' : '' }}>
                                        {{ $item['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3 mt-3">
                            <label for="search_date_from" class="form-label">{{ __('users.created_from') }}</label>
                            <input type="date" class="form-control" id="search_date_from" name="search_date_from"
                                   value="{{ request('search_date_from') }}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label for="search_date_to" class="form-label">{{ __('users.created_to') }}</label>
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

                    // Build search terms display
                    $searchTermsArray = [
                        __('users.name') => request('search_name'),
                        __('users.surname') => request('search_surname'),
                        __('users.email') => request('search_email'),
                        __('users.tax_id_code') => request('search_tax_id')
                    ];

                    // Add company name if filtered
                    if (request('search_company')) {
                        $selectedCompany = $companies->firstWhere('id', request('search_company'));
                        if ($selectedCompany) {
                            $searchTermsArray[__('users.company')] = $selectedCompany->name;
                        }
                    }

                    $searchTerms = collect($searchTermsArray)->filter()->map(function($value, $key) {
                        return $key . ': "' . $value . '"';
                    })->join(', ');
                @endphp
                
                @if($hasAnySearch)
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        {{ __('users.search_results') }}
                        <small class="text-muted">({{ $users->total() }} {{ __('users.found') }})</small>
                    </h5>
                    @if($searchTerms)
                        <p class="mb-0 mt-1 text-muted small">
                            <i class="fas fa-filter me-1"></i>{{ __('users.filtered_by') }}: {{ $searchTerms }}
                            @if(request('search_gender'))
                                , {{ __('users.gender') }}: {{ ucfirst(request('search_gender')) }}
                            @endif
                            @if(request('search_status'))
                                , {{ __('users.status') }}: {{ request('search_status') == 'active' ? __('users.active') : (request('search_status') == 'inactive' ? __('users.inactive') : __('users.parked')) }}
                            @endif
                            @if(request('search_company'))
                                , {{ __('users.company') }}: {{ $companies->find(request('search_company'))->name ?? 'Unknown' }}
                            @endif
                            @if(request('search_date_from') || request('search_date_to'))
                                , Date: {{ request('search_date_from') ?? 'Any' }} to {{ request('search_date_to') ?? 'Any' }}
                            @endif
                        </p>
                    @endif
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        {{ __('users.all_users') }}
                        <small class="text-muted">({{ $users->total() }} {{ __('users.total') }})</small>
                    </h5>
                @endif
            </div>
            
            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('users.add_new_user') }}
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
                    <i class="fas fa-users me-2"></i>{{ __('users.all_users') }}
                </h5>
            </div>
            <div class="card-body">
                @if($isStaManager)
                <form method="POST" action="{{ route('users.bulk-status') }}" id="bulk-status-form" class="mb-3">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <label for="bulk-status-select" class="form-label fw-semibold mb-1">{{ __('users.bulk_status_update') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-sync-alt"></i></span>
                                <select class="form-select form-select-sm" id="bulk-status-select" name="status" required>
                                    <option value="">{{ __('users.bulk_status_select_placeholder') }}</option>
                                    <option value="active">{{ __('users.active') }}</option>
                                    <option value="inactive">{{ __('users.inactive') }}</option>
                                    <option value="parked">{{ __('users.parked') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 ms-auto d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm mt-3 mt-sm-0" id="bulk-status-apply" disabled>
                                <i class="fas fa-check me-2"></i>{{ __('users.bulk_status_apply') }}
                            </button>
                        </div>
                    </div>
                </form>
                @endif
                <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    @if($isStaManager)
                                    <th width="3%" class="text-center">
                                        <input type="checkbox" id="select-all-users" class="form-check-input" title="{{ __('users.bulk_status_select_all') }}">
                                    </th>
                                    @endif
                                    <th width="5%">#</th>
                                <th width="20%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ __('users.name') }}
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
                                        {{ __('users.email') }}
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
                                        {{ __('users.mobile') }}
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
                                        {{ __('users.status') }}
                                        @if(request('sort') === 'status')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="12%">{{ __('users.primary_company') }}</th>
                                <th width="8%">{{ __('users.roles') }}</th>
                                <th width="10%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        {{ __('users.created_at') }}
                                        @if(request('sort') === 'created_at')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="12%">{{ __('users.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="{{ $user->status === 'parked' ? 'user-parked' : '' }}">
                                @if($isStaManager)
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input user-select-checkbox" name="user_ids[]" value="{{ $user->id }}" form="bulk-status-form">
                                </td>
                                @endif
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-lg me-2 text-muted"></i>
                                        <div>
                                            <div class="fw-bold">{{ $user->full_name }}</div>
                                            @if($user->id === auth()->id())
                                                <small class="text-muted">({{ __('users.you') }})</small>
                                            @endif
                                            @if($user->age)
                                                <small class="text-muted">{{ __('users.age') }}: {{ $user->age }}</small>
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
                                        <span class="badge bg-success">{{ __('users.active') }}</span>
                                    @elseif($user->status === 'parked')
                                        <span class="badge bg-warning approval-needed">
                                            <i class="fas fa-clock me-1"></i>{{ __('users.pending_approval') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('users.inactive') }}</span>
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
                                        <span class="badge bg-secondary">{{ __('users.no_role_assigned') }}</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('view users')
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info" title="{{ __('users.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit users')
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('users.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete users')
                                        @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;" 
                                              onsubmit="return confirm('{{ __('users.delete_confirmation') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('users.delete') }}">
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
                                <td colspan="{{ $isStaManager ? 10 : 9 }}" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('users.no_users_found') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($isStaManager)
                </form>
                @endif

                <!-- Pagination and Per Page Controls -->
                @if($users->hasPages() || $users->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">{{ __('users.show') }}:</label>
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
                            <span class="text-muted small">{{ __('users.entries_per_page') }}</span>
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
                        {{ __('users.showing') }} {{ $users->firstItem() ?? 0 }} {{ __('users.to') }} {{ $users->lastItem() ?? 0 }}
                        {{ __('users.of') }} {{ $users->total() }} {{ __('users.results') }}
                    </div>
                    <div>
                        {{ __('users.page') }} {{ $users->currentPage() }} {{ __('users.of') }} {{ $users->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
