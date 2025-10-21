@extends('layouts.advanced-dashboard')

@section('page-title', trans('profile.review_profile_change_request'))

@push('styles')
<style>
    /* Ensure modal covers entire viewport */
    body.modal-open {
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h4 class="page-title">{{ trans('profile.review_profile_change_request') }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}">{{ trans('profile.dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('company-manager.profile-change-requests') }}">{{ trans('profile.profile_change_requests') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ trans('profile.review') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Info Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>{{ trans('profile.user_information') }}
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $changeRequest->user->photo_url }}" alt="{{ $changeRequest->user->full_name }}"
                         class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <h5>{{ $changeRequest->user->full_name }}</h5>
                    <p class="text-muted">{{ $changeRequest->user->email }}</p>

                    <hr>

                    <div class="text-start">
                        <p class="mb-2"><strong>{{ trans('profile.requested_on') }}:</strong></p>
                        <p class="text-muted">{{ $changeRequest->created_at->format('F d, Y \a\t H:i') }}</p>

                        @if($changeRequest->request_message)
                            <p class="mb-2 mt-3"><strong>{{ trans('profile.user_message') }}:</strong></p>
                            <div class="alert alert-info">
                                <i class="fas fa-comment me-2"></i>
                                {{ $changeRequest->request_message }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes Comparison Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>{{ trans('profile.requested_changes') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-light">
                                    <th width="25%">{{ trans('profile.field') }}</th>
                                    <th width="37.5%">{{ trans('profile.current_value') }}</th>
                                    <th width="37.5%">{{ trans('profile.requested_value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changeRequest->getChangedFields() as $field)
                                    @php
                                        $comparison = $changeRequest->getFieldComparison($field);
                                        $currentValue = $comparison['current'];
                                        $requestedValue = $comparison['requested'];

                                        // Format values for display
                                        if ($field === 'date_of_birth') {
                                            $currentValue = $currentValue ? \Carbon\Carbon::parse($currentValue)->format('M d, Y') : '-';
                                            $requestedValue = $requestedValue ? \Carbon\Carbon::parse($requestedValue)->format('M d, Y') : '-';
                                        } elseif ($field === 'gender') {
                                            $currentValue = $currentValue ? trans('profile.' . $currentValue) : '-';
                                            $requestedValue = $requestedValue ? trans('profile.' . $requestedValue) : '-';
                                        } elseif ($field === 'photo') {
                                            // Handle photo specially
                                            $currentValue = '<img src="' . asset('storage/' . $currentValue) . '" width="80" height="80" class="rounded" style="object-fit: cover;">';
                                            $requestedValue = '<img src="' . asset('storage/' . $requestedValue) . '" width="80" height="80" class="rounded" style="object-fit: cover;">';
                                        } else {
                                            $currentValue = $currentValue ?: '-';
                                            $requestedValue = $requestedValue ?: '-';
                                        }
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                        <td>
                                            @if($field === 'photo')
                                                {!! $currentValue !!}
                                            @else
                                                {{ $currentValue }}
                                            @endif
                                        </td>
                                        <td class="table-warning">
                                            @if($field === 'photo')
                                                {!! $requestedValue !!}
                                            @else
                                                <strong>{{ $requestedValue }}</strong>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ trans('profile.review_changes_info') }}
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('company-manager.profile-change-requests') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ trans('profile.back') }}
                        </a>
                        <div>
                            <button type="button" class="btn btn-danger me-2" onclick="showRejectModal()">
                                <i class="fas fa-times me-2"></i>{{ trans('profile.reject_changes') }}
                            </button>
                            <form action="{{ route('company-manager.profile-change-requests.approve', $changeRequest->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ trans('profile.confirm_approve') }}')">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>{{ trans('profile.approve_changes') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Custom Rejection Modal - Appended to body for full screen coverage -->
<div id="customRejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.3); position: relative;">
        <form action="{{ route('company-manager.profile-change-requests.reject', $changeRequest->id) }}" method="POST">
            @csrf
            <!-- Header -->
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="margin: 0; font-size: 1.25rem; font-weight: 500;">{{ trans('profile.reject_changes') }}</h5>
                <button type="button" onclick="hideRejectModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c757d; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    &times;
                </button>
            </div>

            <!-- Body -->
            <div style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label for="rejection_reason" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                        {{ trans('profile.rejection_reason') }}
                        <span style="color: #6c757d;">({{ trans('profile.optional') }})</span>
                    </label>
                    <textarea class="form-control" id="rejection_reason" name="rejection_reason"
                              rows="4"
                              placeholder="{{ trans('profile.rejection_reason_placeholder') }}"
                              style="width: 100%; padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; font-family: inherit; font-size: 1rem;"></textarea>
                    <small style="color: #6c757d; font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        {{ trans('profile.rejection_reason_help') }}
                    </small>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">
                    {{ trans('profile.cancel') }}
                </button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-times me-2"></i>{{ trans('profile.reject') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Append modal to body to ensure full screen coverage
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('customRejectModal');
    if (modal && modal.parentElement.tagName !== 'BODY') {
        document.body.appendChild(modal);
    }
});

function showRejectModal() {
    const modal = document.getElementById('customRejectModal');
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
}

function hideRejectModal() {
    const modal = document.getElementById('customRejectModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    document.body.style.overflow = ''; // Restore scroll
}

// Close modal when clicking on backdrop
document.addEventListener('click', function(e) {
    const modal = document.getElementById('customRejectModal');
    if (e.target === modal) {
        hideRejectModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('customRejectModal');
        if (modal && modal.style.display === 'flex') {
            hideRejectModal();
        }
    }
});
</script>
@endpush
