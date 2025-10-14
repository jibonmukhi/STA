@extends('layouts.advanced-dashboard')

@section('page-title', 'Invite Company Manager')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-envelope me-2"></i>Invite Company Manager
            </h4>
            <div class="btn-group">
                <a href="{{ route('companies.invitations.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-list me-2"></i>View All Invitations
                </a>
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Companies
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>How it works:</strong> Fill in the company and manager details below. An invitation email will be sent to the manager with login credentials and a secure link to accept the invitation. The invitation will expire in 48 hours.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company & Manager Information
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

                <form action="{{ route('companies.invite.send') }}" method="POST">
                    @csrf

                    <!-- Company Information Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-building me-2"></i>Company Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">
                                        Company Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_email" class="form-label">
                                        Company Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('company_email') is-invalid @enderror"
                                           id="company_email" name="company_email" value="{{ old('company_email') }}" required>
                                    @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_phone" class="form-label">Company Phone</label>
                                    <input type="text" class="form-control @error('company_phone') is-invalid @enderror"
                                           id="company_phone" name="company_phone" value="{{ old('company_phone') }}">
                                    @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_piva" class="form-label">P.IVA / VAT Number</label>
                                    <input type="text" class="form-control @error('company_piva') is-invalid @enderror"
                                           id="company_piva" name="company_piva" value="{{ old('company_piva') }}">
                                    @error('company_piva')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_ateco_code" class="form-label">ATECO Code</label>
                                    <input type="text" class="form-control @error('company_ateco_code') is-invalid @enderror"
                                           id="company_ateco_code" name="company_ateco_code" value="{{ old('company_ateco_code') }}"
                                           placeholder="e.g. 620100" maxlength="10">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Economic activity classification code (numeric only)
                                    </div>
                                    @error('company_ateco_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manager Information Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-user-tie me-2"></i>Company Manager Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manager_name" class="form-label">
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('manager_name') is-invalid @enderror"
                                           id="manager_name" name="manager_name" value="{{ old('manager_name') }}" required>
                                    @error('manager_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manager_surname" class="form-label">Surname</label>
                                    <input type="text" class="form-control @error('manager_surname') is-invalid @enderror"
                                           id="manager_surname" name="manager_surname" value="{{ old('manager_surname') }}">
                                    @error('manager_surname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="manager_email" class="form-label">
                                        Manager Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('manager_email') is-invalid @enderror"
                                           id="manager_email" name="manager_email" value="{{ old('manager_email') }}" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>This email will be used to send the invitation and as the login username.
                                    </div>
                                    @error('manager_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> A temporary password will be automatically generated and sent to the manager via email. The manager will be required to change this password upon first login.
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>What happens next?
                </h6>
            </div>
            <div class="card-body">
                <ol class="ps-3">
                    <li class="mb-2">System creates the company and manager account</li>
                    <li class="mb-2">Generates a secure temporary password</li>
                    <li class="mb-2">Sends invitation email with credentials and link</li>
                    <li class="mb-2">Manager receives email and clicks the link</li>
                    <li class="mb-2">Manager logs in with temporary password</li>
                    <li class="mb-2">Manager changes password on first login</li>
                    <li class="mb-2">Manager can start managing the company</li>
                </ol>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Security
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Secure token generation
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Invitation expires in 48 hours
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Password must be changed on first login
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>All actions are logged in audit trail
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
