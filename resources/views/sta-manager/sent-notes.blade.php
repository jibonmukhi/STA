@extends('layouts.advanced-dashboard')

@section('page-title', 'Notes Log - Activity & Tracking')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1"><i class="fas fa-clipboard-list me-2"></i>Notes Log - Activity & Tracking</h3>
                            <p class="card-text opacity-75 mb-0">Complete log of all notes sent to company managers with delivery and read status tracking</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">Total Notes</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['total_notes'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">Today</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['notes_today'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">This Week</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['notes_this_week'] }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-calendar-week fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted">This Month</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['notes_this_month'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sta-manager.sent-notes') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Subject, message, user, company...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="company_filter" class="form-label">Company</label>
                                <select class="form-select" id="company_filter" name="company_filter">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ request('company_filter') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status_filter" class="form-label">Status</label>
                                <select class="form-select" id="status_filter" name="status_filter">
                                    <option value="">All Status</option>
                                    <option value="read" {{ request('status_filter') == 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="unread" {{ request('status_filter') == 'unread' ? 'selected' : '' }}>Unread</option>
                                    <option value="failed" {{ request('status_filter') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1 mb-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        @if(request()->hasAny(['search', 'company_filter', 'status_filter', 'date_from', 'date_to']))
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('sta-manager.sent-notes') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Notes Log ({{ $notes->total() }})</h5>
                    <div class="d-flex align-items-center gap-3">
                        <!-- Per Page Dropdown -->
                        <div class="d-flex align-items-center">
                            <label for="per_page" class="me-2 mb-0 text-nowrap">Rows per page:</label>
                            <select class="form-select form-select-sm" id="per_page" name="per_page" style="width: auto;" onchange="updatePerPage(this.value)">
                                <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($notes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Company</th>
                                        <th>Regarding User</th>
                                        <th>Subject</th>
                                        <th>Sent By</th>
                                        <th>Recipients</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                    <tr>
                                        <td>
                                            <div class="text-nowrap">{{ $note->created_at->format('M d, Y') }}</div>
                                            <div class="small text-muted">{{ $note->created_at->format('H:i:s') }}</div>
                                            <div class="small text-muted">{{ $note->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $note->company->logo_url }}" alt="{{ $note->company->name }}" class="avatar avatar-sm rounded me-2">
                                                <div>
                                                    <strong>{{ $note->company->name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($note->user)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $note->user->photo_url }}" alt="{{ $note->user->full_name }}" class="avatar avatar-sm rounded-circle me-2">
                                                <div>
                                                    <strong>{{ $note->user->full_name }}</strong>
                                                    <br><small class="text-muted">{{ $note->user->email }}</small>
                                                </div>
                                            </div>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $note->subject }}</strong>
                                            <br><small class="text-muted">{{ Str::limit($note->message, 50) }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $note->sender->full_name ?? 'System' }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $tracking = $note->notificationTracking;
                                                $totalRecipients = $tracking->count();
                                                $readCount = $tracking->where('status', 'read')->count();
                                                $unreadCount = $tracking->whereIn('status', ['sent', 'delivered'])->count();
                                                $failedCount = $tracking->where('status', 'failed')->count();
                                            @endphp
                                            <div class="small">
                                                <span class="badge bg-success">{{ $readCount }} Read</span>
                                                <span class="badge bg-warning">{{ $unreadCount }} Unread</span>
                                                @if($failedCount > 0)
                                                <span class="badge bg-danger">{{ $failedCount }} Failed</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($readCount == $totalRecipients && $totalRecipients > 0)
                                                <span class="badge bg-success">All Read</span>
                                            @elseif($failedCount == $totalRecipients)
                                                <span class="badge bg-danger">All Failed</span>
                                            @elseif($readCount > 0)
                                                <span class="badge bg-info">Partially Read</span>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#noteDetailsModal{{ $note->id }}">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $notes->firstItem() ?? 0 }} to {{ $notes->lastItem() ?? 0 }} of {{ $notes->total() }} results
                            </div>
                            {{ $notes->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">No Notes Found</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'company_filter', 'status_filter', 'date_from', 'date_to']))
                                    No notes match your filters.
                                @else
                                    No notes have been sent yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updatePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}
</script>
@endpush

@endsection

@section('modals')
<!-- Note Details Modals -->
@foreach($notes as $note)
<div class="modal fade" id="noteDetailsModal{{ $note->id }}" tabindex="-1" aria-labelledby="noteDetailsModalLabel{{ $note->id }}" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="noteDetailsModalLabel{{ $note->id }}">
                    <i class="fas fa-info-circle me-2"></i>Note Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #ffffff;">
                <div class="row">
                    <!-- Note Information -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-sticky-note me-2 text-primary"></i>Note Information</h6>
                            </div>
                            <div class="card-body bg-white">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150" class="text-muted">Sent At:</th>
                                        <td class="fw-semibold">{{ $note->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Sent By:</th>
                                        <td class="fw-semibold">{{ $note->sender->full_name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Company:</th>
                                        <td class="fw-semibold">{{ $note->company->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Subject:</th>
                                        <td><strong class="text-primary">{{ $note->subject }}</strong></td>
                                    </tr>
                                </table>
                                <div class="mt-3">
                                    <div class="text-muted fw-semibold mb-2">Message:</div>
                                    <div class="border rounded p-3 bg-light">
                                        {{ $note->message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-user me-2 text-success"></i>Regarding User</h6>
                            </div>
                            <div class="card-body bg-white">
                                @if($note->user_data)
                                @php $userData = $note->user_data; @endphp
                                <div class="text-center mb-3">
                                    <img src="{{ $userData['photo_url'] ?? '' }}" alt="{{ $userData['full_name'] ?? '' }}" class="rounded-circle border border-3 border-light shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                                </div>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="120" class="text-muted">Name:</th>
                                        <td><strong class="text-dark">{{ $userData['full_name'] ?? 'N/A' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email:</th>
                                        <td class="fw-semibold">{{ $userData['email'] ?? 'N/A' }}</td>
                                    </tr>
                                    @if(!empty($userData['cf']))
                                    <tr>
                                        <th class="text-muted">CF:</th>
                                        <td class="fw-semibold">{{ $userData['cf'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($userData['phone']))
                                    <tr>
                                        <th class="text-muted">Phone:</th>
                                        <td class="fw-semibold">{{ $userData['phone'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($userData['mobile']))
                                    <tr>
                                        <th class="text-muted">Mobile:</th>
                                        <td class="fw-semibold">{{ $userData['mobile'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($userData['date_of_birth']))
                                    <tr>
                                        <th class="text-muted">DOB (Age):</th>
                                        <td class="fw-semibold">{{ $userData['date_of_birth'] }} ({{ $userData['age'] ?? '' }} years)</td>
                                    </tr>
                                    @endif
                                    @if(!empty($userData['address']))
                                    <tr>
                                        <th class="text-muted">Address:</th>
                                        <td class="fw-semibold">{{ $userData['address'] }}</td>
                                    </tr>
                                    @endif
                                </table>
                                @else
                                <p class="text-muted">No user information available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Tracking -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-bell me-2 text-info"></i>Notification Tracking <span class="badge bg-info">{{ $note->notificationTracking->count() }} recipients</span></h6>
                            </div>
                            <div class="card-body bg-white p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-semibold">Recipient</th>
                                                <th class="fw-semibold">Type</th>
                                                <th class="fw-semibold">Status</th>
                                                <th class="fw-semibold">Sent At</th>
                                                <th class="fw-semibold">Read At</th>
                                                <th class="fw-semibold">Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($note->notificationTracking as $tracking)
                                            <tr>
                                                <td>
                                                    <strong>{{ $tracking->recipient->full_name ?? 'Unknown' }}</strong>
                                                    <br><small class="text-muted">{{ $tracking->recipient->email ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($tracking->notification_type) }}</span>
                                                </td>
                                                <td>
                                                    @if($tracking->status === 'read')
                                                        <span class="badge bg-success">Read</span>
                                                    @elseif($tracking->status === 'delivered')
                                                        <span class="badge bg-info">Delivered</span>
                                                    @elseif($tracking->status === 'sent')
                                                        <span class="badge bg-warning">Sent</span>
                                                    @else
                                                        <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $tracking->sent_at ? $tracking->sent_at->format('M d, H:i') : 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($tracking->read_at)
                                                        <span class="text-success">{{ $tracking->read_at->format('M d, H:i') }}</span>
                                                        <br><small class="text-muted">{{ $tracking->read_at->diffForHumans() }}</small>
                                                    @else
                                                        <span class="text-muted">Not read yet</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tracking->error_message)
                                                        <span class="text-danger small">{{ Str::limit($tracking->error_message, 50) }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No tracking information available</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
