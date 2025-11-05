@extends('layouts.advanced-dashboard')

@section('page-title', __('users.company_users_management'))

@section('content')
<style>
    /* Fix dropdown menu display issues */
    .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 200px; /* Space for dropdowns */
        margin-bottom: -200px; /* Collapse the extra space */
    }

    /* Dropdown menu styling */
    .table-wrapper .dropdown-menu {
        min-width: 180px;
        max-width: 200px;
        white-space: nowrap;
        z-index: 1060 !important;
    }

    .table-wrapper .dropdown-menu .dropdown-item {
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        white-space: nowrap;
    }

    /* Prevent scrollbars from appearing */
    .card-body {
        overflow: visible !important;
    }

    .card {
        overflow: visible !important;
    }

    /* Make table responsive on mobile */
    @media (max-width: 768px) {
        .table-wrapper {
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid">
    @include('components.flash-messages')

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ __('users.company_users_management') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ __('users.company_users_subtitle') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('company-users.create') }}" class="btn btn-light">
                                <i class="fas fa-user-plus me-1"></i> {{ __('users.add_user') }}
                            </a>
                            <a href="{{ route('my-companies.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-building me-1"></i> {{ __('users.my_companies') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('company-users.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="search_name" class="form-label">{{ __('users.name') }}</label>
                                <input type="text" class="form-control" id="search_name" name="search_name"
                                       value="{{ request('search_name') }}" placeholder="{{ __('users.search_by_name') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="search_email" class="form-label">{{ __('users.email') }}</label>
                                <input type="email" class="form-control" id="search_email" name="search_email"
                                       value="{{ request('search_email') }}" placeholder="{{ __('users.search_by_email') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="company" class="form-label">{{ __('users.company') }}</label>
                                <select class="form-select" id="company" name="company">
                                    <option value="">{{ __('users.all_companies') }}</option>
                                    @foreach($userCompanies as $company)
                                        <option value="{{ $company->id }}"
                                                {{ request('company') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">{{ __('users.status') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">{{ __('users.all_status') }}</option>
                                    @foreach(dataVaultItems('user_status') as $item)
                                        <option value="{{ $item['code'] }}" {{ request('status') == $item['code'] ? 'selected' : '' }}>
                                            {{ $item['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('company-users.index') }}" class="btn btn-outline-secondary">
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

    <!-- Users Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.total_users') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $users->total() }}</h4>
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
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.active_users') }}</h6>
                            <h4 class="mb-0 text-success">{{ $users->where('status', 'active')->count() }}</h4>
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
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.pending') }}</h6>
                            <h4 class="mb-0 text-warning">{{ $users->where('status', 'parked')->count() }}</h4>
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
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ __('users.companies') }}</h6>
                            <h4 class="mb-0 text-info">{{ $userCompanies->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        {{ __('users.users_list') }}
                        @if(request()->filled('company'))
                            <span class="badge bg-info ms-2">
                                {{ $userCompanies->find(request('company'))->name ?? 'Unknown Company' }}
                            </span>
                        @endif
                        <span class="badge bg-warning ms-2" id="selectedCount" style="display: none;">
                            <span id="selectedCountText">0</span> {{ __('users.selected') }}
                        </span>
                    </h5>
                    <div class="d-flex gap-2">
                        @if($users->where('status', 'parked')->count() > 0)
                            <button type="button" class="btn btn-success" id="sendForApprovalBtn" onclick="sendForApproval()" disabled>
                                <i class="fas fa-paper-plane me-1"></i> {{ __('users.send_for_approval') }}
                            </button>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i> {{ __('users.export') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportUsers('pdf')">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportUsers('excel')">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportUsers('csv')">
                                    <i class="fas fa-file-csv me-2"></i>CSV
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-wrapper">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>{{ __('users.user') }}</th>
                                        <th>{{ __('users.email') }}</th>
                                        <th>{{ __('users.phone') }}</th>
                                        <th>{{ __('users.companies') }}</th>
                                        <th>{{ __('users.role') }}</th>
                                        <th>{{ __('users.status') }}</th>
                                        <th>{{ __('users.joined') }}</th>
                                        <th width="80" class="text-center">{{ __('users.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            @if($user->status === 'parked')
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                           value="{{ $user->id }}" id="user_{{ $user->id }}">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                                     class="avatar avatar-sm rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold">{{ $user->full_name }}</div>
                                                    @if($user->cf)
                                                        <small class="text-muted">{{ $user->cf }}</small>
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
                                            @if($user->phone)
                                                <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                                    {{ $user->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('users.na') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($user->companies as $company)
                                                    <span class="badge bg-{{ $company->pivot->is_primary ? 'primary' : 'secondary' }}">
                                                        {{ $company->name }}
                                                        @if($company->pivot->percentage)
                                                            ({{ $company->pivot->percentage }}%)
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->roles->isNotEmpty())
                                                <span class="badge bg-info">{{ $user->formatted_role }}</span>
                                            @else
                                                <span class="text-muted">{{ __('users.no_role') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'parked' ? 'warning' : ($user->status === 'pending_approval' ? 'info' : 'secondary')) }}">
                                                {{ __('users.' . $user->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" onclick="viewUser({{ $user->id }})">
                                                        <i class="fas fa-eye me-2"></i>{{ __('users.view_details') }}
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="editUser({{ $user->id }})">
                                                        <i class="fas fa-edit me-2"></i>{{ __('common.edit') }}
                                                    </a></li>
                                                    @if($user->status === 'pending_approval')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="cancelRequest({{ $user->id }}, '{{ addslashes($user->full_name) }}')">
                                                            <i class="fas fa-times-circle me-2"></i>{{ __('users.cancel_request') }}
                                                        </a></li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-info" href="#" onclick="sendMessage({{ $user->id }})">
                                                        <i class="fas fa-envelope me-2"></i>{{ __('users.send_message') }}
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination and Per Page -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="d-flex align-items-center gap-2">
                                <form method="GET" action="{{ route('company-users.index') }}" class="d-flex align-items-center">
                                    @if(request('search_name'))
                                        <input type="hidden" name="search_name" value="{{ request('search_name') }}">
                                    @endif
                                    @if(request('search_email'))
                                        <input type="hidden" name="search_email" value="{{ request('search_email') }}">
                                    @endif
                                    @if(request('company'))
                                        <input type="hidden" name="company" value="{{ request('company') }}">
                                    @endif
                                    @if(request('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
                                    <select name="per_page" class="form-select form-select-sm me-2" onchange="this.form.submit()" style="width: auto;">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="text-muted small">{{ __('users.entries_per_page') }}</span>
                                </form>
                                <div class="text-muted ms-3">
                                    {{ __('users.showing') }} {{ $users->firstItem() }} {{ __('users.to') }} {{ $users->lastItem() }} {{ __('users.of') }} {{ $users->total() }} {{ __('users.results') }}
                                </div>
                            </div>
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">{{ __('users.no_users_found_title') }}</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['search_name', 'search_email', 'company', 'status']))
                                    {{ __('users.no_users_match_criteria') }}
                                @else
                                    {{ __('users.no_users_in_companies') }}
                                @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(request()->hasAny(['search_name', 'search_email', 'company', 'status']))
                                    <a href="{{ route('company-users.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-times me-1"></i> {{ __('users.clear_filters') }}
                                    </a>
                                @endif
                                <a href="{{ route('company-users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> {{ __('users.add_first_user') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Select all checkbox functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Update select all when individual checkboxes change
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();

            const allCheckboxes = document.querySelectorAll('.user-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
            }
        });
    });

    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        const selectedCountBadge = document.getElementById('selectedCount');
        const selectedCountText = document.getElementById('selectedCountText');
        const sendBtn = document.getElementById('sendForApprovalBtn');

        if (count > 0) {
            selectedCountBadge.style.display = 'inline-block';
            selectedCountText.textContent = count;
            if (sendBtn) sendBtn.disabled = false;
        } else {
            selectedCountBadge.style.display = 'none';
            if (sendBtn) sendBtn.disabled = true;
        }
    }

    function sendForApproval() {
        const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);

        if (selectedUsers.length === 0) {
            alert('{{ __('users.select_at_least_one_company') }}');
            return;
        }

        const confirmMsg = '{{ __('users.send_for_approval_confirm', ['count' => '__COUNT__']) }}'.replace('__COUNT__', selectedUsers.length);
        if (confirm(confirmMsg)) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('company-users.send-for-approval') }}';

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

    function viewUser(userId) {
        window.location.href = `/company-users/${userId}`;
    }

    function editUser(userId) {
        window.location.href = `/company-users/${userId}/edit`;
    }

    function sendMessage(userId) {
        alert('{{ __('users.messaging_feature_coming_soon') }}');
    }

    function cancelRequest(userId, userName) {
        if (confirm('{{ __('users.confirm_cancel_request') }}')) {
            // Create and submit form for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/company-users/${userId}`;

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function exportUsers(format) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', format);
        alert(`Exporting users as ${format.toUpperCase()}...\nURL: ${window.location.pathname}?${params.toString()}`);
    }
</script>
@endsection