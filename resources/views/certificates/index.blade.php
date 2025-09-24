@extends('layouts.advanced-dashboard')

@section('page-title', __('certificates.certificate_management'))

@push('styles')
<style>
.table th a {
    color: inherit;
    text-decoration: none;
}
.table th a:hover {
    color: var(--bs-primary);
}
.sorting-icon {
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
.certificate-badge {
    font-size: 0.75em;
}
.expiring-soon {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
.stats-card {
    transition: transform 0.2s;
}
.stats-card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@section('content')
<!-- Display Flash Messages -->
@include('components.flash-messages')

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">{{ __('certificates.total_certificates') }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <i class="fas fa-certificate fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">{{ __('certificates.active_certificates') }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['active']) }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">{{ __('certificates.expired_certificates') }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['expired']) }}</h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">{{ __('certificates.expiring_soon_certificates') }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['expiring_soon']) }}</h3>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card search-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>{{ __('certificates.search_certificates') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('certificates.index') }}">
                    <div class="row g-3">
                        <!-- Certificate Name Search -->
                        <div class="col-md-4">
                            <label for="search_name" class="form-label">{{ __('certificates.certificate_name') }}</label>
                            <input type="text" class="form-control" id="search_name" name="search_name"
                                   placeholder="{{ __('certificates.search_by_name') }}" value="{{ request('search_name') }}">
                        </div>

                        <!-- Subject Search -->
                        <div class="col-md-4">
                            <label for="search_subject" class="form-label">{{ __('certificates.subject') }}</label>
                            <input type="text" class="form-control" id="search_subject" name="search_subject"
                                   placeholder="{{ __('certificates.search_by_subject') }}" value="{{ request('search_subject') }}">
                        </div>

                        <!-- Organization Search -->
                        <div class="col-md-4">
                            <label for="search_organization" class="form-label">{{ __('certificates.training_organization') }}</label>
                            <input type="text" class="form-control" id="search_organization" name="search_organization"
                                   placeholder="{{ __('certificates.search_by_organization') }}" value="{{ request('search_organization') }}">
                        </div>
                    </div>

                    <!-- Advanced Search Options -->
                    <div class="row g-3 mt-2" id="advanced-search" style="display: none;">
                        <div class="col-md-3">
                            <label for="search_type" class="form-label">{{ __('certificates.certificate_type') }}</label>
                            <select class="form-select" id="search_type" name="search_type">
                                <option value="">{{ __('certificates.filter_by_type') }}</option>
                                @foreach($certificateTypes as $key => $value)
                                    <option value="{{ $key }}" {{ request('search_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="search_status" class="form-label">{{ __('certificates.status') }}</label>
                            <select class="form-select" id="search_status" name="search_status">
                                <option value="">{{ __('certificates.filter_by_status') }}</option>
                                @foreach($certificateStatuses as $key => $value)
                                    <option value="{{ $key }}" {{ request('search_status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($companies->count() > 0)
                        <div class="col-md-3">
                            <label for="search_company" class="form-label">{{ __('certificates.company') }}</label>
                            <select class="form-select" id="search_company" name="search_company">
                                <option value="">{{ __('certificates.filter_by_company') }}</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('search_company') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label for="search_expiration_from" class="form-label">{{ __('certificates.expiration_from') }}</label>
                            <input type="date" class="form-control" id="search_expiration_from" name="search_expiration_from"
                                   value="{{ request('search_expiration_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="search_expiration_to" class="form-label">{{ __('certificates.expiration_to') }}</label>
                            <input type="date" class="form-control" id="search_expiration_to" name="search_expiration_to"
                                   value="{{ request('search_expiration_to') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>{{ __('certificates.search') }}
                                </button>
                                @if(request()->hasAny(['search_name', 'search_subject', 'search_organization', 'search_type', 'search_status', 'search_company', 'search_expiration_from', 'search_expiration_to']))
                                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>{{ __('certificates.clear_all') }}
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-info ms-auto" onclick="toggleAdvancedSearch()">
                                    <i class="fas fa-cog me-1"></i>{{ __('certificates.advanced_options') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preserve pagination parameters -->
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
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
                    $hasAnySearch = request()->hasAny(['search_name', 'search_subject', 'search_organization', 'search_type', 'search_status', 'search_company', 'search_expiration_from', 'search_expiration_to']);
                @endphp

                @if($hasAnySearch)
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        {{ __('certificates.search_results') }}
                        <small class="text-muted">({{ $certificates->total() }} {{ __('certificates.found') }})</small>
                    </h5>
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        {{ __('certificates.all_certificates') }}
                        <small class="text-muted">({{ $certificates->total() }} {{ __('certificates.total') }})</small>
                    </h5>
                @endif
            </div>

            @can('create', App\Models\Certificate::class)
            <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('certificates.add_new_certificate') }}
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Certificates Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-certificate me-2"></i>{{ __('certificates.certificates') }}
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
                                        {{ __('certificates.name') }}
                                        @if(request('sort') === 'name')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted sorting-icon"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="20%">{{ __('certificates.subject_column') }}</th>
                                <th width="15%">{{ __('certificates.organization') }}</th>
                                <th width="10%">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'expiration_date', 'direction' => request('sort') === 'expiration_date' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-decoration-none text-dark">
                                        {{ __('certificates.expiration_date_column') }}
                                        @if(request('sort') === 'expiration_date')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted sorting-icon"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="10%">{{ __('certificates.status_column') }}</th>
                                <th width="15%">{{ __('certificates.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-certificate fa-lg me-2 text-{{ $certificate->status_badge_class === 'bg-success' ? 'success' : ($certificate->status_badge_class === 'bg-danger' ? 'danger' : 'warning') }}"></i>
                                        <div>
                                            <div class="fw-bold">{{ $certificate->name }}</div>
                                            <small class="text-muted">{{ $certificate->certificate_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $certificate->subject }}</span>
                                    @if($certificate->certificate_type)
                                        <div><small class="badge bg-light text-dark">{{ $certificate->formatted_type }}</small></div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $certificate->training_organization }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $certificate->expiration_date->format('M d, Y') }}</span>
                                    @if($certificate->is_expiring_soon)
                                        <div><small class="text-warning expiring-soon">{{ __('certificates.expires_in_days', ['days' => $certificate->days_until_expiration]) }}</small></div>
                                    @elseif($certificate->is_expired)
                                        <div><small class="text-danger">{{ __('certificates.expired_days_ago', ['days' => abs($certificate->days_until_expiration)]) }}</small></div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $certificate->status_badge_class }}">{{ $certificate->formatted_status }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- View Button -->
                                        <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-sm btn-outline-info" title="{{ __('certificates.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        @can('update', $certificate)
                                        <a href="{{ route('certificates.edit', $certificate) }}" class="btn btn-sm btn-outline-primary" title="{{ __('certificates.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan

                                        <!-- Download Button -->
                                        @if($certificate->hasFile())
                                        <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-sm btn-outline-success" title="{{ __('certificates.download') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif

                                        <!-- Delete Button -->
                                        @can('delete', $certificate)
                                        <form method="POST" action="{{ route('certificates.destroy', $certificate) }}" style="display: inline;"
                                              onsubmit="return confirm('{{ __('certificates.delete_confirmation') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('certificates.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('certificates.no_certificates_found') }}</p>
                                    @can('create', App\Models\Certificate::class)
                                        <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>{{ __('certificates.create_certificate_btn') }}
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Per Page Controls -->
                @if($certificates->hasPages() || $certificates->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">{{ __('certificates.show') }}:</label>
                        <form method="GET" action="{{ route('certificates.index') }}" class="d-flex align-items-center">
                            <!-- Preserve search parameters -->
                            @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach

                            <select name="per_page" class="form-select form-select-sm me-2" style="width: auto;" onchange="this.form.submit()">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-muted small">{{ __('certificates.entries_per_page') }}</span>
                        </form>
                    </div>

                    <!-- Pagination (Right) -->
                    @if($certificates->hasPages())
                    <div>
                        {{ $certificates->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <div>
                        {{ __('certificates.showing') }} {{ $certificates->firstItem() ?? 0 }} {{ __('certificates.to') }} {{ $certificates->lastItem() ?? 0 }}
                        {{ __('certificates.of') }} {{ $certificates->total() }} {{ __('certificates.results') }}
                    </div>
                    <div>
                        {{ __('certificates.page') }} {{ $certificates->currentPage() }} {{ __('certificates.of') }} {{ $certificates->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Advanced search toggle
function toggleAdvancedSearch() {
    const advancedDiv = document.getElementById('advanced-search');
    const toggleBtn = document.querySelector('button[onclick="toggleAdvancedSearch()"]');

    if (advancedDiv.style.display === 'none' || advancedDiv.style.display === '') {
        advancedDiv.style.display = 'flex';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('certificates.hide_advanced') }}';
        toggleBtn.classList.remove('btn-outline-info');
        toggleBtn.classList.add('btn-info');
    } else {
        advancedDiv.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-cog me-1"></i>{{ __('certificates.advanced_options') }}';
        toggleBtn.classList.remove('btn-info');
        toggleBtn.classList.add('btn-outline-info');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Show advanced search if any advanced fields have values
    const hasAdvancedValues = {{ request()->hasAny(['search_type', 'search_status', 'search_company', 'search_expiration_from', 'search_expiration_to']) ? 'true' : 'false' }};
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
    const searchForm = document.querySelector('form[action*="certificates"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __('certificates.searching') }}';
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