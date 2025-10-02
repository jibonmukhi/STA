@extends('layouts.advanced-dashboard')

@section('page-title', 'Audit Logs')

@push('styles')
<style>
    .audit-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .activity-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 14px;
    }
    .stats-card {
        border-left: 4px solid;
    }
    .stats-card.border-primary {
        border-left-color: var(--bs-primary);
    }
    .stats-card.border-success {
        border-left-color: var(--bs-success);
    }
    .stats-card.border-info {
        border-left-color: var(--bs-info);
    }
    .stats-card.border-warning {
        border-left-color: var(--bs-warning);
    }
    .filter-section {
        background-color: #f8f9fa;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-history me-2"></i>Audit Logs
                    </h4>
                    <p class="text-muted mb-0">Track all user activities and system changes</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="toggleFilters()">
                        <i class="fas fa-filter me-1"></i>Filters
                    </button>
                    @can('export', App\Models\AuditLog::class)
                    <button type="button" class="btn btn-outline-success" onclick="exportLogs()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    @endcan
                    @can('delete', App\Models\AuditLog::class)
                    <a href="{{ route('audit-logs.cleanup') }}" class="btn btn-outline-danger">
                        <i class="fas fa-broom me-1"></i>Cleanup
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="activity-icon bg-primary text-white me-3">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Today's Activities</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_today']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="activity-icon bg-success text-white me-3">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">This Week</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_week']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="activity-icon bg-info text-white me-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">This Month</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_month']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="activity-icon bg-warning text-white me-3">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Most Active User</h6>
                            <h6 class="mb-0">{{ $stats['most_active_user']->user_name ?? 'N/A' }}</h6>
                            @if($stats['most_active_user'])
                            <small class="text-muted">{{ number_format($stats['most_active_user']->count) }} activities</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4" id="filtersSection" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body filter-section">
                    <form method="GET" action="{{ route('audit-logs.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="Search in logs...">
                            </div>

                            <!-- User Filter -->
                            <div class="col-md-2">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Action Filter -->
                            <div class="col-md-2">
                                <label for="action" class="form-label">Action</label>
                                <select class="form-select" id="action" name="action">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $action)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Module Filter -->
                            <div class="col-md-2">
                                <label for="module" class="form-label">Module</label>
                                <select class="form-select" id="module" name="module">
                                    <option value="">All Modules</option>
                                    @foreach($modules as $module)
                                        @if($module)
                                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Severity Filter -->
                            <div class="col-md-1">
                                <label for="severity" class="form-label">Severity</label>
                                <select class="form-select" id="severity" name="severity">
                                    <option value="">All</option>
                                    <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="error" {{ request('severity') == 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>

                            <!-- Date To -->
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>

                            <!-- Model Type Filter -->
                            <div class="col-md-2">
                                <label for="model_type" class="form-label">Model Type</label>
                                <select class="form-select" id="model_type" name="model_type">
                                    <option value="">All Types</option>
                                    @foreach($modelTypes as $type)
                                        <option value="App\Models\{{ $type }}" {{ request('model_type') == "App\\Models\\{$type}" ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div class="col-md-2">
                                <label for="per_page" class="form-label">Per Page</label>
                                <select class="form-select" id="per_page" name="per_page" onchange="this.form.submit()">
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Apply Filters
                                    </button>
                                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="150">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Date/Time
                                                @if(request('sort') == 'created_at')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Module</th>
                                        <th>IP Address</th>
                                        <th width="100">Severity</th>
                                        <th width="80">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $log->created_at->format('Y-m-d') }}<br>
                                                    {{ $log->created_at->format('H:i:s') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($log->user)
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="fw-semibold">{{ $log->user_name }}</div>
                                                            <small class="text-muted">{{ $log->user_role }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->action_color }} audit-badge">
                                                    {{ $log->formatted_action }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;" title="{{ $log->activity_description }}">
                                                    {{ $log->activity_description }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($log->module)
                                                    <span class="badge bg-secondary audit-badge">
                                                        {{ ucfirst($log->module) }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->ip_address }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log->severity_color }} audit-badge">
                                                    {{ ucfirst($log->severity) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                            </div>
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No audit logs found</h5>
                            <p class="text-muted">Try adjusting your filters or search criteria</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilters() {
    const filtersSection = document.getElementById('filtersSection');
    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
    } else {
        filtersSection.style.display = 'none';
    }
}

function exportLogs() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    window.location.href = "{{ route('audit-logs.export') }}?" + params;
}

// Show filters if any filter is active
document.addEventListener('DOMContentLoaded', function() {
    const hasFilters = {{ request()->hasAny(['search', 'user_id', 'action', 'module', 'severity', 'date_from', 'date_to', 'model_type']) ? 'true' : 'false' }};
    if (hasFilters) {
        document.getElementById('filtersSection').style.display = 'block';
    }
});
</script>
@endpush