@extends('layouts.advanced-dashboard')

@section('page-title', __('companies.company_invitations'))

@push('styles')
<style>
.status-badge {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
}
.invitation-card {
    transition: all 0.3s;
}
.invitation-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.stats-card {
    border-left: 4px solid;
    transition: transform 0.2s;
}
.stats-card:hover {
    transform: translateY(-2px);
}
.stats-pending { border-left-color: #ffc107; }
.stats-accepted { border-left-color: #28a745; }
.stats-expired { border-left-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-envelope-open-text me-2"></i>{{ __('companies.company_invitations') }}
            </h4>
            <div class="btn-group">
                <a href="{{ route('companies.invite.form') }}" class="btn btn-success">
                    <i class="fas fa-paper-plane me-2"></i>{{ __('companies.send_new_invitation') }}
                </a>
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-building me-2"></i>{{ __('companies.back_to_companies') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card stats-pending">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">{{ __('companies.pending') }}</p>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card stats-accepted">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['accepted'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">{{ __('companies.accepted') }}</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card stats-expired">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['expired'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">{{ __('companies.expired') }}</p>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card" style="border-left-color: #6c757d;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">{{ __('companies.total') }}</p>
                    </div>
                    <div class="text-secondary">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@include('components.flash-messages')

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('companies.invitations.index') }}" class="row g-3">
                    <!-- Hidden input to preserve per_page value -->
                    <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">

                    <div class="col-md-3">
                        <label for="filter_status" class="form-label">{{ __('companies.status') }}</label>
                        <select name="filter_status" id="filter_status" class="form-select">
                            <option value="">{{ __('companies.all_statuses') }}</option>
                            <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>{{ __('companies.pending') }}</option>
                            <option value="accepted" {{ request('filter_status') == 'accepted' ? 'selected' : '' }}>{{ __('companies.accepted') }}</option>
                            <option value="expired" {{ request('filter_status') == 'expired' ? 'selected' : '' }}>{{ __('companies.expired') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search_company" class="form-label">{{ __('companies.company_name') }}</label>
                        <input type="text" name="search_company" id="search_company" class="form-control"
                               value="{{ request('search_company') }}" placeholder="{{ __('companies.search_by_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search_email" class="form-label">{{ __('companies.manager_email') }}</label>
                        <input type="text" name="search_email" id="search_email" class="form-control"
                               value="{{ request('search_email') }}" placeholder="{{ __('companies.search_by_email') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-2"></i>{{ __('companies.filter') }}
                            </button>
                            <a href="{{ route('companies.invitations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>{{ __('companies.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Invitations List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>{{ __('companies.all_invitations') }} ({{ $invitations->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($invitations->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{ __('companies.no_invitations_found') }}</p>
                        <a href="{{ route('companies.invite.form') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('companies.send_first_invitation') }}
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('companies.company_name') }}</th>
                                    <th>{{ __('companies.company_email') }}</th>
                                    <th>{{ __('companies.manager') }}</th>
                                    <th>{{ __('companies.manager_email') }}</th>
                                    <th>{{ __('companies.status') }}</th>
                                    <th>{{ __('companies.sent_date') }}</th>
                                    <th>{{ __('companies.expires_at') }}</th>
                                    <th>{{ __('companies.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invitations as $invitation)
                                <tr>
                                    <td>
                                        <strong>{{ $invitation->company_name }}</strong>
                                        @if($invitation->company_piva)
                                            <br><small class="text-muted">{{ __('companies.piva') }}: {{ $invitation->company_piva }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $invitation->company_email }}</td>
                                    <td>{{ $invitation->manager_name }} {{ $invitation->manager_surname }}</td>
                                    <td>{{ $invitation->manager_email }}</td>
                                    <td>{!! $invitation->status_badge !!}</td>
                                    <td>
                                        <small>{{ $invitation->created_at->format('M j, Y') }}</small><br>
                                        <small class="text-muted">{{ $invitation->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($invitation->isExpired())
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ __('companies.expired') }}
                                            </span><br>
                                            <small class="text-muted">{{ $invitation->expires_at->diffForHumans() }}</small>
                                        @elseif($invitation->isAccepted())
                                            <span class="text-success">
                                                <i class="fas fa-check me-1"></i>{{ __('companies.accepted') }}
                                            </span><br>
                                            <small class="text-muted">{{ $invitation->accepted_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-warning">
                                                <i class="fas fa-clock me-1"></i>{{ $invitation->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($invitation->isPending())
                                                <!-- Resend Invitation -->
                                                <form action="{{ route('companies.invitations.resend', $invitation->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('companies.are_you_sure_resend') }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success"
                                                            title="{{ __('companies.resend_invitation') }}">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>

                                                <!-- Copy Link -->
                                                <button type="button" class="btn btn-info"
                                                        onclick="copyInvitationLink('{{ route('invitation.accept', $invitation->token) }}')"
                                                        title="{{ __('companies.copy_invitation_link') }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            @endif

                                            @if($invitation->isAccepted())
                                                <!-- View Company -->
                                                <a href="{{ route('companies.show', $invitation->company_id) }}"
                                                   class="btn btn-primary" title="{{ __('companies.view_company') }}">
                                                    <i class="fas fa-building"></i>
                                                </a>

                                                <!-- View User -->
                                                <a href="{{ route('users.show', $invitation->user_id) }}"
                                                   class="btn btn-secondary" title="{{ __('companies.view_user') }}">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                            @endif

                                            <!-- View Details -->
                                            <a href="{{ route('companies.invitations.details', $invitation->id) }}"
                                               class="btn btn-outline-primary"
                                               title="{{ __('companies.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($invitation->isPending() || $invitation->isExpired())
                                                <!-- Delete Invitation -->
                                                <form action="{{ route('companies.invitations.destroy', $invitation->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('companies.are_you_sure_delete') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                            title="{{ __('companies.delete_invitation') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <!-- Rows Per Page Dropdown (Left) -->
                        <div class="d-flex align-items-center">
                            <label for="per_page" class="mb-0 me-2">{{ __('companies.rows_per_page') }}</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="ms-3 text-muted">
                                {{ __('companies.showing') }} {{ $invitations->firstItem() ?? 0 }} {{ __('companies.to') }} {{ $invitations->lastItem() ?? 0 }} {{ __('companies.of') }} {{ $invitations->total() }} {{ __('companies.invitations') }}
                            </span>
                        </div>

                        <!-- Pagination Links (Right) -->
                        <div>
                            {{ $invitations->links() }}
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
function copyInvitationLink(url, id = null) {
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        alert('Invitation link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link. Please copy manually.');
    });
}

function changePerPage(perPage) {
    // Get current URL
    const url = new URL(window.location.href);

    // Update or add per_page parameter
    url.searchParams.set('per_page', perPage);

    // Reset to first page when changing per_page
    url.searchParams.delete('page');

    // Redirect to new URL
    window.location.href = url.toString();
}
</script>
@endpush
