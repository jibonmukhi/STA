@extends('layouts.advanced-dashboard')

@section('page-title', __('users.pending_user_approvals'))

@push('styles')
<style>
    /* No custom z-index - let Bootstrap handle it naturally */
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ __('users.pending_user_approvals') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ __('users.review_approve_registrations') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-users me-1"></i> {{ __('users.all_users') }}
                            </a>
                            <a href="{{ route('sta.dashboard') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('users.dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.total_pending') }}</h6>
                            <h4 class="mb-0 text-warning">{{ $stats['total_pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.this_week') }}</h6>
                            <h4 class="mb-0 text-info">{{ $stats['pending_this_week'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.this_month') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['pending_this_month'] }}</h4>
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
                    <form method="GET" action="{{ route('users.pending.approvals') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="search" class="form-label">{{ __('users.search_users') }}</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="{{ __('users.search_by_email') }}...">
                            </div>
                            <div class="col-md-4">
                                <label for="company_filter" class="form-label">{{ __('users.company') }}</label>
                                <select class="form-select" id="company_filter" name="company_filter">
                                    <option value="">{{ __('users.all_companies') }}</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}"
                                                {{ request('company_filter') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> {{ __('users.search') }}
                                    </button>
                                    <a href="{{ route('users.pending.approvals') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Users -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        {{ __('users.users_awaiting_approval') }}
                        @if(request()->filled('search') || request()->filled('company_filter'))
                            <span class="badge bg-info ms-2">{{ __('users.filtered') }}</span>
                        @endif
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i> {{ __('users.bulk_actions') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="bulkApprove()">
                                <i class="fas fa-check me-2 text-success"></i>{{ __('users.approve_selected') }}
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="bulkReject()">
                                <i class="fas fa-times me-2 text-danger"></i>{{ __('users.reject_selected') }}
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @if($pendingUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label" for="selectAll">{{ __('users.select_all') }}</label>
                                            </div>
                                        </th>
                                        <th>{{ __('users.user_details') }}</th>
                                        <th>{{ __('users.contact') }}</th>
                                        <th>{{ __('users.company') }}</th>
                                        <th>{{ __('users.registration_date') }}</th>
                                        <th>{{ __('users.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input user-checkbox" type="checkbox"
                                                       value="{{ $user->id }}" id="user_{{ $user->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                                     class="avatar avatar-lg rounded-circle me-3">
                                                <div>
                                                    <div class="fw-bold h6 mb-1">{{ $user->full_name }}</div>
                                                    @if($user->cf)
                                                        <div class="small text-muted">CF: {{ $user->cf }}</div>
                                                    @endif
                                                    @if($user->date_of_birth)
                                                        <div class="small text-muted">
                                                            Born: {{ $user->date_of_birth->format('M d, Y') }}
                                                            ({{ $user->age }} years old)
                                                        </div>
                                                    @endif
                                                    @if($user->place_of_birth)
                                                        <div class="small text-muted">{{ $user->place_of_birth }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div class="mb-1">
                                                    <i class="fas fa-envelope text-muted me-1" style="width: 16px;"></i>
                                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                        {{ $user->email }}
                                                    </a>
                                                </div>
                                                @if($user->phone)
                                                    <div class="mb-1">
                                                        <i class="fas fa-phone text-muted me-1" style="width: 16px;"></i>
                                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                                            {{ $user->phone }}
                                                        </a>
                                                    </div>
                                                @endif
                                                @if($user->address)
                                                    <div>
                                                        <i class="fas fa-map-marker-alt text-muted me-1" style="width: 16px;"></i>
                                                        <small class="text-muted">{{ Str::limit($user->address, 30) }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->companies->isNotEmpty())
                                                @foreach($user->companies as $company)
                                                    <div class="company-item mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                                                 class="avatar avatar-xs rounded me-2">
                                                            <div>
                                                                <div class="fw-bold small">{{ $company->name }}</div>
                                                                <div class="text-muted small">
                                                                    {{ $company->pivot->role_in_company ?? 'Member' }}
                                                                    @if($company->pivot->percentage)
                                                                        ({{ $company->pivot->percentage }}%)
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No Company</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ $user->created_at->format('M d, Y') }}</div>
                                            <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <form action="{{ route('users.approve', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                            onclick="return confirm('Are you sure you want to approve {{ $user->full_name }}?')">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>

                                                <form action="{{ route('users.reject', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to reject and delete {{ $user->full_name }}? This action cannot be undone.')">
                                                        <i class="fas fa-times me-1"></i> Reject
                                                    </button>
                                                </form>

                                                <button class="btn btn-outline-info btn-sm" onclick="viewUserDetails({{ $user->id }})">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>

                                                @if($user->companies->isNotEmpty())
                                                    @php
                                                        $firstCompany = $user->companies->first();
                                                    @endphp
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#sendNoteModal{{ $firstCompany->id }}_{{ $user->id }}"
                                                            title="Send note to {{ $firstCompany->name }}">
                                                        <i class="fas fa-sticky-note me-1"></i> Note
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $pendingUsers->firstItem() }} to {{ $pendingUsers->lastItem() }} of {{ $pendingUsers->total() }} results
                            </div>
                            {{ $pendingUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-check text-success" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-success">No Pending Approvals!</h5>
                            <p class="text-muted mb-4">
                                @if(request()->filled('search') || request()->filled('company_filter'))
                                    No pending users match your search criteria.
                                @else
                                    All user registrations are up to date. Great job!
                                @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(request()->filled('search') || request()->filled('company_filter'))
                                    <a href="{{ route('users.pending.approvals') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('users.index') }}" class="btn btn-primary">
                                    <i class="fas fa-users me-1"></i> View All Users
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Send Note Modals (placed at body level to avoid any container conflicts) -->
@if($pendingUsers->count() > 0)
    @foreach($pendingUsers as $user)
        @if($user->companies->isNotEmpty())
            @php
                $firstCompany = $user->companies->first();
            @endphp
            <div class="modal fade" id="sendNoteModal{{ $firstCompany->id }}_{{ $user->id }}" tabindex="-1" aria-labelledby="sendNoteModalLabel{{ $firstCompany->id }}_{{ $user->id }}" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('companies.send-note', $firstCompany) }}" id="noteForm{{ $firstCompany->id }}_{{ $user->id }}">
                            @csrf
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title" id="sendNoteModalLabel{{ $firstCompany->id }}_{{ $user->id }}">
                                    <i class="fas fa-sticky-note me-2"></i>Send Note to Company Manager
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Detailed User Information Card -->
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-user me-2"></i><strong>Regarding User - Complete Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->full_name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Full Name:</small><br>
                                                        <strong>{{ $user->full_name }}</strong>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Email:</small><br>
                                                        <strong>{{ $user->email }}</strong>
                                                    </div>
                                                    @if($user->cf)
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Codice Fiscale:</small><br>
                                                        <strong>{{ $user->cf }}</strong>
                                                    </div>
                                                    @endif
                                                    @if($user->date_of_birth)
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Date of Birth (Age):</small><br>
                                                        <strong>{{ $user->date_of_birth->format('d/m/Y') }} ({{ $user->age }} years)</strong>
                                                    </div>
                                                    @endif
                                                    @if($user->phone)
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Phone:</small><br>
                                                        <strong>{{ $user->phone }}</strong>
                                                    </div>
                                                    @endif
                                                    @if($user->mobile)
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted">Mobile:</small><br>
                                                        <strong>{{ $user->mobile }}</strong>
                                                    </div>
                                                    @endif
                                                    @if($user->address)
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted">Address:</small><br>
                                                        <strong>{{ $user->address }}</strong>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Company Details -->
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="text-info mb-2"><i class="fas fa-building me-2"></i>Company Details:</h6>
                                            @foreach($user->companies as $userCompany)
                                            <div class="d-flex align-items-center mb-2 ps-3">
                                                <img src="{{ $userCompany->logo_url }}" alt="{{ $userCompany->name }}" class="avatar avatar-sm rounded me-2">
                                                <div>
                                                    <strong>{{ $userCompany->name }}</strong>
                                                    @if($userCompany->pivot && $userCompany->pivot->percentage)
                                                    <span class="badge bg-primary ms-2">{{ $userCompany->pivot->percentage }}%</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">
                                                        Role: <strong>{{ $userCompany->pivot->role_in_company ?? 'Member' }}</strong>
                                                    </small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                @if($user->companies->count() > 1)
                                <div class="mb-3">
                                    <label for="company_select{{ $firstCompany->id }}_{{ $user->id }}" class="form-label">
                                        Select Company <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="company_select{{ $firstCompany->id }}_{{ $user->id }}" required onchange="updateFormAction{{ $firstCompany->id }}_{{ $user->id }}(this.value)">
                                        @foreach($user->companies as $company)
                                            <option value="{{ $company->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">This user belongs to multiple companies. Select which company to send the note to.</div>
                                </div>

                                <script>
                                    function updateFormAction{{ $firstCompany->id }}_{{ $user->id }}(companyId) {
                                        const form = document.getElementById('noteForm{{ $firstCompany->id }}_{{ $user->id }}');
                                        form.action = '{{ url('companies') }}/' + companyId + '/send-note';
                                    }
                                </script>
                                @else
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $firstCompany->logo_url }}" alt="{{ $firstCompany->name }}" class="avatar avatar-sm rounded me-2">
                                        <div>
                                            <strong>{{ $firstCompany->name }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @php
                                    // Get company managers for display
                                    $selectedCompany = $user->companies->count() > 1 ? null : $firstCompany;
                                    $companyManagers = $selectedCompany ? $selectedCompany->users->filter(function($user) {
                                        return $user->hasRole('company_manager');
                                    }) : collect();
                                @endphp

                                @if($selectedCompany && $companyManagers->isNotEmpty())
                                <div class="alert alert-warning">
                                    <strong><i class="fas fa-users me-2"></i>Recipients ({{ $companyManagers->count() }} manager{{$companyManagers->count() > 1 ? 's' : ''}}):</strong>
                                    <div class="mt-2">
                                        @foreach($companyManagers as $manager)
                                            <div class="mb-1">
                                                <i class="fas fa-envelope me-1 text-muted"></i>
                                                <strong>{{ $manager->full_name }}</strong>: {{ $manager->email }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @elseif($user->companies->count() > 1)
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Select a company above to see the manager's email addresses</small>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label for="subject{{ $firstCompany->id }}_{{ $user->id }}" class="form-label">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="subject{{ $firstCompany->id }}_{{ $user->id }}" name="subject" required maxlength="255" placeholder="e.g., Missing document for user approval">
                                </div>
                                <div class="mb-3">
                                    <label for="message{{ $firstCompany->id }}_{{ $user->id }}" class="form-label">
                                        Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="message{{ $firstCompany->id }}_{{ $user->id }}" name="message" rows="6" required maxlength="2000" placeholder="Please specify what is missing or needs attention..."></textarea>
                                    <div class="form-text">
                                        <i class="fas fa-envelope me-1"></i>This note will be sent to all managers of the selected company.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-paper-plane me-1"></i>Send Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

<script>
    // Select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.user-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');

            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        });
    });

    function bulkApprove() {
        const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);

        if (selectedUsers.length === 0) {
            alert('Please select at least one user to approve.');
            return;
        }

        if (confirm(`Are you sure you want to approve ${selectedUsers.length} selected user(s)?`)) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('users.bulk-approve') }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add selected user IDs
            selectedUsers.forEach(userId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = userId;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }

    function bulkReject() {
        const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);

        if (selectedUsers.length === 0) {
            alert('Please select at least one user to reject.');
            return;
        }

        if (confirm(`Are you sure you want to reject and delete ${selectedUsers.length} selected user(s)? This action cannot be undone.`)) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('users.bulk-reject') }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add selected user IDs
            selectedUsers.forEach(userId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = userId;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }

    function viewUserDetails(userId) {
        // Navigate to user details page
        window.location.href = '{{ url('users') }}/' + userId;
    }

    // Ensure all modals are properly initialized and can be interacted with
    document.addEventListener('DOMContentLoaded', function() {
        // Get all modal elements
        const modalElements = document.querySelectorAll('.modal');

        modalElements.forEach(function(modalEl) {
            // Ensure modal is appended to body if not already
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            // Add event listener to ensure modal works
            modalEl.addEventListener('shown.bs.modal', function () {
                // Focus on first input when modal opens
                const firstInput = this.querySelector('input[type="text"], textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    });
</script>
@endsection