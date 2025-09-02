@extends('layouts.advanced-dashboard')

@section('page-title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->full_name }}
            </h4>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>User Information
                </h5>
            </div>
            <div class="card-body">
                <!-- Display Flash Messages -->
                @include('components.flash-messages')

                <!-- Display Validation Errors -->
                @if($errors->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any() && !$errors->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                       id="surname" name="surname" value="{{ old('surname', $user->surname) }}">
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tax_id_code" class="form-label">Tax ID Code</label>
                                <input type="text" class="form-control @error('tax_id_code') is-invalid @enderror" 
                                       id="tax_id_code" name="tax_id_code" value="{{ old('tax_id_code', $user->tax_id_code) }}">
                                @error('tax_id_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-address-card me-2"></i>Contact Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Phone</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                       id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Company Associations Section -->
                    @if($companies->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-building me-2"></i>Company Associations
                            </h6>
                        </div>
                    </div>

                    @php
                        $userCompanyIds = old('companies', $user->companies->pluck('id')->toArray());
                        $primaryCompanyId = old('primary_company', $user->companies->where('pivot.is_primary', true)->first()->id ?? null);
                    @endphp

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Associated Companies</label>
                                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
                                    @foreach($companies as $company)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="companies[]" 
                                               value="{{ $company->id }}" id="company_{{ $company->id }}"
                                               {{ in_array($company->id, $userCompanyIds) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex justify-content-between" for="company_{{ $company->id }}">
                                            <span>{{ $company->name }}</span>
                                            <small class="text-muted">{{ $company->email }}</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Select companies this user should be associated with.</div>
                                @error('companies')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="primary_company" class="form-label">Primary Company</label>
                                <select class="form-select @error('primary_company') is-invalid @enderror" 
                                        id="primary_company" name="primary_company">
                                    <option value="">Select Primary Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $primaryCompanyId == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">The main company for this user.</div>
                                @error('primary_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Current Companies Display -->
                    @if($user->companies->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Current Company Associations</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($user->companies as $company)
                                        <div class="badge {{ $company->pivot->is_primary ? 'bg-primary' : 'bg-secondary' }} p-2">
                                            {{ $company->name }}
                                            @if($company->pivot->is_primary)
                                                <i class="fas fa-star ms-1" title="Primary Company"></i>
                                            @endif
                                            @if($company->pivot->role_in_company)
                                                <br><small>{{ $company->pivot->role_in_company }}</small>
                                            @endif
                                            @if($company->pivot->joined_at)
                                                <br><small>Joined: {{ \Carbon\Carbon::parse($company->pivot->joined_at)->format('M d, Y') }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif

                    <!-- System Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-cog me-2"></i>System Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <div class="form-text">Leave blank to keep current password</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                           {{ old('status', $user->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        Active User
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Account Information</label>
                                <div class="small text-muted">
                                    <div>Created: {{ $user->created_at->format('M d, Y H:i') }}</div>
                                    <div>Last Updated: {{ $user->updated_at->format('M d, Y H:i') }}</div>
                                    @if($user->age)
                                        <div>Age: {{ $user->age }} years old</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($roles->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">User Roles</label>
                                <div class="row">
                                    @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                                   value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync company checkboxes with primary company dropdown
    const companyCheckboxes = document.querySelectorAll('input[name="companies[]"]');
    const primaryCompanySelect = document.getElementById('primary_company');
    
    if (primaryCompanySelect) {
        companyCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updatePrimaryCompanyOptions();
            });
        });
        
        primaryCompanySelect.addEventListener('change', function() {
            if (this.value) {
                // Automatically check the company if it's selected as primary
                const primaryCheckbox = document.querySelector(`input[name="companies[]"][value="${this.value}"]`);
                if (primaryCheckbox && !primaryCheckbox.checked) {
                    primaryCheckbox.checked = true;
                }
            }
        });
    }
    
    function updatePrimaryCompanyOptions() {
        if (!primaryCompanySelect) return;
        
        const checkedCompanies = Array.from(companyCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        // Disable options for unchecked companies
        Array.from(primaryCompanySelect.options).forEach(function(option) {
            if (option.value && !checkedCompanies.includes(option.value)) {
                option.disabled = true;
                if (option.selected) {
                    primaryCompanySelect.value = '';
                }
            } else {
                option.disabled = false;
            }
        });
    }
    
    // Initial update
    updatePrimaryCompanyOptions();
});
</script>
@endsection