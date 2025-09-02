@extends('layouts.advanced-dashboard')

@section('page-title', 'Create New Role')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Create New Role</h2>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Roles
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Role Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required placeholder="Enter role name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($permissions->count() > 0)
                    <div class="mb-4">
                        <label class="form-label">Permissions <span class="text-muted">(Select all that apply)</span></label>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select_all">
                                    <label class="form-check-label fw-bold" for="select_all">
                                        Select All Permissions
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="row">
                                @php
                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                        return explode(' ', $permission->name)[1] ?? 'general';
                                    });
                                @endphp

                                @foreach($groupedPermissions as $group => $perms)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary text-uppercase">{{ ucfirst($group) }} Permissions</h6>
                                    @foreach($perms as $permission)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                               value="{{ $permission->name }}" id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            <i class="fas fa-key text-muted me-2"></i>{{ $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Guidelines
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Use descriptive role names (e.g., "Content Manager", "HR Assistant")
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Only assign necessary permissions for the role
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Review permissions carefully before creating
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Role names should be unique and clear
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select_all');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === permissionCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < permissionCheckboxes.length;
        });
    });
});
</script>
@endsection