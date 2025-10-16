@extends('layouts.advanced-dashboard')

@section('page-title', __('audit.my_activity_log'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ __('audit.my_activity_log') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ __('audit.track_actions') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('company-manager.profile') }}" class="btn btn-outline-light">
                                <i class="fas fa-user me-1"></i> {{ __('audit.my_profile') }}
                            </a>
                            <a href="{{ route('company-users.index') }}" class="btn btn-light">
                                <i class="fas fa-users me-1"></i> {{ __('audit.company_users') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('audit.today') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['total_today'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('audit.this_week') }}</h6>
                            <h4 class="mb-0 text-info">{{ $stats['total_week'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('audit.this_month') }}</h6>
                            <h4 class="mb-0 text-warning">{{ $stats['total_month'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-history"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('audit.total') }}</h6>
                            <h4 class="mb-0 text-success">{{ $stats['total_all'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('company-manager.audit-logs') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">{{ __('audit.search') }}</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="{{ __('audit.search_placeholder') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="action" class="form-label">{{ __('audit.action') }}</label>
                                <select class="form-select" id="action" name="action">
                                    <option value="">{{ __('audit.all_actions') }}</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ __('audit_actions.' . $action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="module" class="form-label">{{ __('audit.module') }}</label>
                                <select class="form-select" id="module" name="module">
                                    <option value="">{{ __('audit.all_modules') }}</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ __('audit_actions.' . $module) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">{{ __('audit.from_date') }}</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">{{ __('audit.to_date') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> {{ __('audit.filter') }}
                                    </button>
                                    <a href="{{ route('company-manager.audit-logs') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> {{ __('audit.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            {{ __('audit.activity_history') }}
                            @if(request()->filled('search') || request()->filled('action') || request()->filled('module') || request()->filled('date_from') || request()->filled('date_to'))
                                <span class="badge bg-info ms-2">{{ __('audit.filtered') }}</span>
                            @endif
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Per Page Dropdown -->
                        <div class="d-flex align-items-center">
                            <label for="per_page" class="me-2 mb-0 text-nowrap">{{ __('audit.rows_per_page') }}:</label>
                            <select class="form-select form-select-sm" id="per_page" name="per_page" style="width: auto;" onchange="updatePerPage(this.value)">
                                <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="text-muted">
                            <small>{{ __('audit.showing') }} {{ $logs->firstItem() ?? 0 }} {{ __('audit.to') }} {{ $logs->lastItem() ?? 0 }} {{ __('audit.of') }} {{ $logs->total() }} {{ __('audit.results') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($logs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('audit.time') }}</th>
                                        <th>{{ __('audit.action') }}</th>
                                        <th>{{ __('audit.description') }}</th>
                                        <th>{{ __('audit.module') }}</th>
                                        <th>{{ __('audit.details') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            <div class="text-nowrap">{{ $log->created_at->format('M d, Y') }}</div>
                                            <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                            <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->action_color }}">
                                                {{ __('audit_actions.' . $log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $log->activity_description }}</div>
                                            @if($log->model_type_name && $log->model_type_name !== '-')
                                                <div class="small text-muted">
                                                    <i class="fas fa-tag me-1"></i>{{ $log->model_type_name }}
                                                    @if($log->model_id)
                                                        #{{ $log->model_id }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->module)
                                                <span class="badge bg-secondary">{{ __('audit_actions.' . $log->module) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->hasRecordedChanges())
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#logDetailsModal{{ $log->id }}">
                                                    <i class="fas fa-eye me-1"></i> {{ __('audit.view_changes') }}
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                {{ __('audit.showing') }} {{ $logs->firstItem() }} {{ __('audit.to') }} {{ $logs->lastItem() }} {{ __('audit.of') }} {{ $logs->total() }} {{ __('audit.results') }}
                            </div>
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">{{ __('audit.no_activity_found') }}</h5>
                            <p class="text-muted mb-4">
                                @if(request()->filled('search') || request()->filled('action') || request()->filled('module') || request()->filled('date_from') || request()->filled('date_to'))
                                    {{ __('audit.no_match_filters') }}
                                @else
                                    {{ __('audit.no_activity_message') }}
                                @endif
                            </p>
                            @if(request()->filled('search') || request()->filled('action') || request()->filled('module') || request()->filled('date_from') || request()->filled('date_to'))
                                <a href="{{ route('company-manager.audit-logs') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-times me-1"></i> {{ __('audit.clear_filters') }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modals -->
@foreach($logs as $log)
    @if($log->hasRecordedChanges())
        <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1" aria-labelledby="logDetailsModalLabel{{ $log->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="logDetailsModalLabel{{ $log->id }}">
                            <i class="fas fa-info-circle me-2"></i>{{ __('audit.activity_details') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6>{{ __('audit.activity_information') }}</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">{{ __('audit.time') }}:</th>
                                    <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('audit.action') }}:</th>
                                    <td><span class="badge bg-{{ $log->action_color }}">{{ __('audit_actions.' . $log->action) }}</span></td>
                                </tr>
                                <tr>
                                    <th>{{ __('audit.module') }}:</th>
                                    <td>{{ __('audit_actions.' . $log->module) }}</td>
                                </tr>
                                @if($log->model_type_name !== '-')
                                    <tr>
                                        <th>{{ __('audit.target') }}:</th>
                                        <td>{{ $log->model_type_name }} #{{ $log->model_id }}</td>
                                    </tr>
                                @endif
                                @if($log->ip_address)
                                    <tr>
                                        <th>{{ __('audit.ip_address') }}:</th>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="mb-3">
                            <h6>{{ __('audit.changes_made') }}</h6>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th width="150">{{ __('audit.field') }}</th>
                                        <th>{{ __('audit.old_value') }}</th>
                                        <th>{{ __('audit.new_value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($log->getFormattedChanges() as $field => $change)
                                        <tr>
                                            <td><strong>{{ $field }}</strong></td>
                                            <td>
                                                @if($change['old'] === '<em>empty</em>')
                                                    <em class="text-muted">{{ __('audit.empty') }}</em>
                                                @else
                                                    <span class="text-danger">{{ $change['old'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($change['new'] === '<em>empty</em>')
                                                    <em class="text-muted">{{ __('audit.empty') }}</em>
                                                @else
                                                    <span class="text-success">{{ $change['new'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($log->description)
                            <div class="alert alert-info mb-0">
                                <strong>{{ __('audit.note') }}:</strong> {{ $log->description }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('audit.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
<script>
function updatePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to first page when changing per_page
    window.location.href = url.toString();
}
</script>
@endpush
@endsection
