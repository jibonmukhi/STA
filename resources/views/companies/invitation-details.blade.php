@extends('layouts.advanced-dashboard')

@section('page-title', 'Invitation Details')

@push('styles')
<style>
.detail-card {
    border-left: 4px solid #007bff;
}
.info-label {
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}
.info-value {
    color: #212529;
    margin-bottom: 1rem;
}
.timeline-item {
    padding-left: 2rem;
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
}
.timeline-item:not(:last-child):after {
    content: '';
    position: absolute;
    left: 5px;
    top: 17px;
    width: 2px;
    height: calc(100% - 12px);
    background: #dee2e6;
}
.invitation-link-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    word-break: break-all;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Invitation Details
            </h4>
            <a href="{{ route('companies.invitations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Invitations
            </a>
        </div>
    </div>
</div>

<!-- Flash Messages -->
@include('components.flash-messages')

<!-- Status Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card detail-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-3">{{ $invitation->company_name }}</h5>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <span class="info-label">Status:</span>
                                {!! $invitation->status_badge !!}
                            </div>
                            <div>
                                <span class="info-label">Sent:</span>
                                <span class="text-muted">{{ $invitation->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($invitation->isAccepted())
                            <div>
                                <span class="info-label">Accepted:</span>
                                <span class="text-success">{{ $invitation->accepted_at->diffForHumans() }}</span>
                            </div>
                            @elseif($invitation->isExpired())
                            <div>
                                <span class="info-label">Expired:</span>
                                <span class="text-danger">{{ $invitation->expires_at->diffForHumans() }}</span>
                            </div>
                            @else
                            <div>
                                <span class="info-label">Expires:</span>
                                <span class="text-warning">{{ $invitation->expires_at->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            @if($invitation->isPending())
                                <!-- Resend Invitation -->
                                <form action="{{ route('companies.invitations.resend', $invitation->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to resend this invitation?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-redo me-2"></i>Resend Invitation
                                    </button>
                                </form>
                            @endif

                            @if($invitation->isAccepted())
                                <!-- View Company -->
                                <a href="{{ route('companies.show', $invitation->company_id) }}"
                                   class="btn btn-primary">
                                    <i class="fas fa-building me-2"></i>View Company
                                </a>

                                <!-- View User -->
                                <a href="{{ route('users.show', $invitation->user_id) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-user me-2"></i>View User
                                </a>
                            @endif

                            @if($invitation->isPending() || $invitation->isExpired())
                                <!-- Delete Invitation -->
                                <form action="{{ route('companies.invitations.destroy', $invitation->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invitation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Cards -->
<div class="row">
    <!-- Company Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Information
                </h5>
            </div>
            <div class="card-body">
                <div class="info-label">Company Name</div>
                <div class="info-value">{{ $invitation->company_name }}</div>

                <div class="info-label">Company Email</div>
                <div class="info-value">
                    <a href="mailto:{{ $invitation->company_email }}">{{ $invitation->company_email }}</a>
                </div>

                @if($invitation->company_phone)
                <div class="info-label">Company Phone</div>
                <div class="info-value">{{ $invitation->company_phone }}</div>
                @endif

                @if($invitation->company_piva)
                <div class="info-label">P.IVA</div>
                <div class="info-value">{{ $invitation->company_piva }}</div>
                @endif

                @if($invitation->company_ateco_code)
                <div class="info-label">ATECO Code</div>
                <div class="info-value">{{ $invitation->company_ateco_code }}</div>
                @endif

                @if($invitation->isAccepted() && $invitation->company)
                <div class="mt-3">
                    <a href="{{ route('companies.show', $invitation->company_id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-2"></i>View Full Company Profile
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Manager Information -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-tie me-2"></i>Manager Information
                </h5>
            </div>
            <div class="card-body">
                <div class="info-label">Manager Name</div>
                <div class="info-value">{{ $invitation->manager_name }} {{ $invitation->manager_surname }}</div>

                <div class="info-label">Manager Email</div>
                <div class="info-value">
                    <a href="mailto:{{ $invitation->manager_email }}">{{ $invitation->manager_email }}</a>
                </div>

                <div class="info-label">Username</div>
                <div class="info-value">
                    <code>{{ $invitation->manager_username }}</code>
                </div>

                @if($invitation->isAccepted() && $invitation->user)
                <div class="mt-3">
                    <a href="{{ route('users.show', $invitation->user_id) }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-external-link-alt me-2"></i>View User Profile
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Invitation Timeline -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Timeline
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline-item">
                    <div class="info-label">Invitation Created</div>
                    <div class="info-value">
                        {{ $invitation->created_at->format('M j, Y g:i A') }}
                        <small class="text-muted">({{ $invitation->created_at->diffForHumans() }})</small>
                    </div>
                    @if($invitation->inviter)
                    <small class="text-muted">Invited by: {{ $invitation->inviter->name }}</small>
                    @endif
                </div>

                <div class="timeline-item">
                    <div class="info-label">Expiration Date</div>
                    <div class="info-value">
                        {{ $invitation->expires_at->format('M j, Y g:i A') }}
                        @if($invitation->isExpired())
                            <span class="badge bg-danger ms-2">Expired</span>
                        @else
                            <small class="text-muted">({{ $invitation->expires_at->diffForHumans() }})</small>
                        @endif
                    </div>
                </div>

                @if($invitation->isAccepted())
                <div class="timeline-item">
                    <div class="info-label">Accepted Date</div>
                    <div class="info-value">
                        {{ $invitation->accepted_at->format('M j, Y g:i A') }}
                        <small class="text-muted">({{ $invitation->accepted_at->diffForHumans() }})</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Invitation Link -->
    @if($invitation->isPending())
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-link me-2"></i>Invitation Link
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Share this link with the company manager to accept the invitation:</p>

                <div class="invitation-link-box" id="invitationLinkBox">
                    {{ route('invitation.accept', $invitation->token) }}
                </div>

                <button type="button" class="btn btn-info mt-3 w-100"
                        onclick="copyInvitationLink('{{ route('invitation.accept', $invitation->token) }}')">
                    <i class="fas fa-copy me-2"></i>Copy Invitation Link
                </button>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>This link will expire on {{ $invitation->expires_at->format('M j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Additional Information -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Additional Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Invitation ID</div>
                        <div class="info-value"><code>#{{ $invitation->id }}</code></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Token (Last 8 chars)</div>
                        <div class="info-value"><code>...{{ substr($invitation->token, -8) }}</code></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ $invitation->updated_at->format('M j, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyInvitationLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        alert('Invitation link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link. Please copy manually.');
    });
}
</script>
@endpush
