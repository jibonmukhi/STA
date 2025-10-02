@extends('layouts.advanced-dashboard')

@section('page-title', 'Company Users')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">Company Users Management</h3>
                            <p class="card-text opacity-75 mb-0">Manage users across all your companies</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('company-users.create') }}" class="btn btn-light">
                                <i class="fas fa-user-plus me-1"></i> Add User
                            </a>
                            <a href="{{ route('my-companies.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-building me-1"></i> My Companies
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
                                <label for="search_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="search_name" name="search_name"
                                       value="{{ request('search_name') }}" placeholder="Search by name...">
                            </div>
                            <div class="col-md-3">
                                <label for="search_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="search_email" name="search_email"
                                       value="{{ request('search_email') }}" placeholder="Search by email...">
                            </div>
                            <div class="col-md-2">
                                <label for="company" class="form-label">Company</label>
                                <select class="form-select" id="company" name="company">
                                    <option value="">All Companies</option>
                                    @foreach($userCompanies as $company)
                                        <option value="{{ $company->id }}"
                                                {{ request('company') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
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
                                        <i class="fas fa-search"></i> Search
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
                            <h6 class="mb-0">Total Users</h6>
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
                            <h6 class="mb-0">Active Users</h6>
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
                            <h6 class="mb-0">Pending</h6>
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
                            <h6 class="mb-0">Companies</h6>
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
                        Users List
                        @if(request()->filled('company'))
                            <span class="badge bg-info ms-2">
                                {{ $userCompanies->find(request('company'))->name ?? 'Unknown Company' }}
                            </span>
                        @endif
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i> Export
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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Companies</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                                                     class="avatar avatar-sm rounded-circle me-2">
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
                                                <span class="text-muted">N/A</span>
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
                                                <span class="text-muted">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'parked' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewUser({{ $user->id }})">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="editUser({{ $user->id }})">
                                                        <i class="fas fa-edit me-2"></i>Edit User
                                                    </a></li>
                                                    @if($user->status === 'parked')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-success" href="#" onclick="approveUser({{ $user->id }})">
                                                            <i class="fas fa-check me-2"></i>Approve User
                                                        </a></li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-info" href="#" onclick="sendMessage({{ $user->id }})">
                                                        <i class="fas fa-envelope me-2"></i>Send Message
                                                    </a></li>
                                                </ul>
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
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                            </div>
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No Users Found</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['search_name', 'search_email', 'company', 'status']))
                                    No users match your current search criteria.
                                @else
                                    No users are assigned to your companies yet.
                                @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(request()->hasAny(['search_name', 'search_email', 'company', 'status']))
                                    <a href="{{ route('company-users.index') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-times me-1"></i> Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('company-users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Add First User
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
    function viewUser(userId) {
        alert(`Viewing details for user ID: ${userId}\nThis would show detailed user information.`);
    }

    function editUser(userId) {
        alert(`Editing user ID: ${userId}\nThis would open an edit form for the user.`);
    }

    function approveUser(userId) {
        if (confirm('Are you sure you want to approve this user?')) {
            alert(`Approving user ID: ${userId}\nThis would activate the user account.`);
            // In a real implementation, this would make an AJAX call to approve the user
        }
    }

    function sendMessage(userId) {
        alert(`Sending message to user ID: ${userId}\nThis would open a messaging interface.`);
    }

    function exportUsers(format) {
        const params = new URLSearchParams(window.location.search);
        params.set('export', format);
        alert(`Exporting users as ${format.toUpperCase()}...\nURL: ${window.location.pathname}?${params.toString()}`);
    }
</script>
@endsection