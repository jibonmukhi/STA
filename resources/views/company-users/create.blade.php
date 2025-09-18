@extends('layouts.advanced-dashboard')

@section('page-title', 'Add Company User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">Add Company User</h3>
                            <p class="card-text opacity-75 mb-0">Create a new user and assign them to your companies</p>
                        </div>
                        <div>
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('company-users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="surname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror"
                                       id="surname" name="surname" value="{{ old('surname') }}" required>
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">Place of Birth</label>
                                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror"
                                       id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}">
                                @error('place_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country') }}" maxlength="2"
                                       placeholder="US, IT, FR...">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">2-letter country code (e.g., US, IT, FR)</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cf" class="form-label">Codice Fiscale</label>
                                <input type="text" class="form-control @error('cf') is-invalid @enderror"
                                       id="cf" name="cf" value="{{ old('cf') }}">
                                @error('cf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role and Access -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-shield me-2"></i>Role and Access
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">System Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ App\Models\User::formatRoleName($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">If no role is selected, user will be assigned as End User</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role_in_company" class="form-label">Role in Company</label>
                                <input type="text" class="form-control @error('role_in_company') is-invalid @enderror"
                                       id="role_in_company" name="role_in_company" value="{{ old('role_in_company', 'Employee') }}">
                                @error('role_in_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">e.g., Manager, Employee, Consultant, etc.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Assignment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>Company Assignment
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($companies->count() > 0)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Companies <span class="text-danger">*</span></label>
                                    @foreach($companies as $company)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="companies[]" value="{{ $company->id }}"
                                                   id="company_{{ $company->id }}"
                                                   {{ in_array($company->id, old('companies', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="company_{{ $company->id }}">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                                         class="avatar avatar-xs rounded me-2">
                                                    <div>
                                                        <div class="fw-bold">{{ $company->name }}</div>
                                                        <small class="text-muted">{{ $company->email }}</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                    @error('companies')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="percentage" class="form-label">Ownership Percentage</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('percentage') is-invalid @enderror"
                                               id="percentage" name="percentage" value="{{ old('percentage', 0) }}"
                                               min="0" max="100" step="0.01">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter ownership percentage (0-100)</div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> The user will be created with status "Parked" and will need approval from an STA Manager before becoming active.
                                Default password will be <strong>password123</strong> - user should change it on first login.
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>No Companies Available:</strong> You don't have access to any companies. Please contact your administrator.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning">
                                    <i class="fas fa-redo me-1"></i> Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary" {{ $companies->count() === 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-user-plus me-1"></i> Create User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-check first company if only one company is available
    document.addEventListener('DOMContentLoaded', function() {
        const companyCheckboxes = document.querySelectorAll('input[name="companies[]"]');
        if (companyCheckboxes.length === 1) {
            companyCheckboxes[0].checked = true;
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedCompanies = document.querySelectorAll('input[name="companies[]"]:checked');

        if (selectedCompanies.length === 0) {
            e.preventDefault();
            alert('Please select at least one company for the user.');
            return false;
        }

        // Confirm user creation
        if (!confirm('Are you sure you want to create this user?\n\nThe user will be created with status "Parked" and default password "password123".')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection