@extends('layouts.advanced-dashboard')

@section('page-title', 'Role Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Role Details</h2>
            <div class="btn-group">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Roles
                </a>
                @can('edit roles')
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Role
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-shield me-2"></i>Role Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role Name</label>
                            <p class="fw-bold fs-4">{{ $role->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Role ID</label>
                            <p class="fw-bold">#{{ $role->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Created Date</label>
                            <p class="fw-bold">{{ $role->created_at->format('F d, Y') }}</p>
                            <small class="text-muted">{{ $role->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Last Updated</label>
                            <p class="fw-bold">{{ $role->updated_at->format('F d, Y') }}</p>
                            <small class="text-muted">{{ $role->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Users Count</label>
                    <p>
                        <span class="badge bg-info fs-6">{{ $role->users()->count() }} Users</span>
                        @if($role->users()->count() > 0)
                            <span class="text-muted ms-2">assigned to this role</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>Assigned Permissions
                </h5>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return explode(' ', $permission->name)[1] ?? 'general';
                        });
                    @endphp

                    <div class="row">
                        @foreach($groupedPermissions as $group => $permissions)
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary text-uppercase fw-bold">{{ ucfirst($group) }} Permissions</h6>
                            @foreach($permissions as $permission)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="badge bg-success me-2">{{ $permission->name }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No permissions assigned to this role.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Role Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ $role->permissions->count() }}</h4>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-1">{{ $role->users()->count() }}</h4>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users with this Role -->
        @if($role->users()->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>Users with this Role
                </h5>
            </div>
            <div class="card-body">
                @foreach($role->users()->take(5)->get() as $user)
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                    <div>
                        <div class="fw-bold">{{ $user->name }}</div>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                </div>
                @endforeach
                @if($role->users()->count() > 5)
                <p class="text-muted small mb-0">
                    And {{ $role->users()->count() - 5 }} more users...
                </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('edit roles')
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Role
                    </a>
                    @endcan
                    
                    @can('delete roles')
                    @if($role->users()->count() === 0)
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Role
                        </button>
                    </form>
                    @else
                    <button class="btn btn-outline-secondary w-100" disabled title="Cannot delete role with assigned users">
                        <i class="fas fa-lock me-2"></i>Cannot Delete (Users Assigned)
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection