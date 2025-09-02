{{-- 
    Reusable Data Table Template
    
    Required props:
    - $title: Page title
    - $items: Paginated collection
    - $routePrefix: Route prefix (e.g., 'companies')
    - $searchFields: Array of searchable fields
    - $sortFields: Array of sortable fields
    
    Slots:
    - actions: Action buttons (create button, etc.)
    - table-headers: Custom table headers
    - table-row: Custom table row content
    - filters: Additional filters (optional)
--}}

@props([
    'title',
    'items', 
    'routePrefix',
    'searchFields' => [],
    'searchPlaceholder' => 'Search...'
])

<!-- Display Flash Messages -->
@include('components.flash-messages')

<!-- Search Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route($routePrefix . '.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="{{ $searchPlaceholder }}" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                    </div>
                    <!-- Preserve other parameters -->
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                    
                    {{ $filters ?? '' }}
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
                @if(request('search'))
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Search Results for "{{ request('search') }}" 
                        <small class="text-muted">({{ $items->total() }} found)</small>
                    </h5>
                @else
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        {{ $title }}
                        <small class="text-muted">({{ $items->total() }} total)</small>
                    </h5>
                @endif
            </div>
            
            {{ $actions ?? '' }}
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ $title }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        {{ $tableHeaders }}
                        <tbody>
                            {{ $tableRows }}
                        </tbody>
                    </table>
                </div>

                <!-- Pagination and Per Page Controls -->
                @if($items->hasPages() || $items->total() > 5)
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Per Page Dropdown (Left) -->
                    <div class="d-flex align-items-center">
                        <label class="form-label me-2 mb-0">Show:</label>
                        <form method="GET" action="{{ route($routePrefix . '.index') }}" class="d-flex align-items-center">
                            <!-- Preserve search and sort parameters -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
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
                    @if($items->hasPages())
                    <div>
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <div>
                        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} 
                        of {{ $items->total() }} results
                        @if(request('search'))
                            for "{{ request('search') }}"
                        @endif
                    </div>
                    <div>
                        Page {{ $items->currentPage() }} of {{ $items->lastPage() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>