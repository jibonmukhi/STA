@extends('layouts.advanced-dashboard')

@section('page-title', __('companies.invite_company_manager'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-envelope me-2"></i>{{ __('companies.invite_company_manager') }}
            </h4>
            <div class="btn-group">
                <a href="{{ route('companies.invitations.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-list me-2"></i>{{ __('companies.view_all_invitations') }}
                </a>
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('companies.back_to_companies') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>{{ __('companies.how_it_works') }}</strong> {{ __('companies.invitation_instructions') }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('companies.company_manager_information') }}
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
                        <strong>{{ __('companies.please_fix_errors') }}</strong>
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
                            <i class="fas fa-building me-2"></i>{{ __('companies.company_information') }}
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">
                                        {{ __('companies.company_name') }} <span class="text-danger">*</span>
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
                                        {{ __('companies.company_email') }} <span class="text-danger">*</span>
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
                                    <label for="company_phone" class="form-label">{{ __('companies.company_phone') }}</label>
                                    <input type="text" class="form-control @error('company_phone') is-invalid @enderror"
                                           id="company_phone" name="company_phone" value="{{ old('company_phone') }}">
                                    @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_piva" class="form-label">{{ __('companies.vat_number') }}</label>
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
                                    <label for="company_ateco_code" class="form-label">{{ __('companies.ateco_code') }}</label>
                                    <input type="text" class="form-control @error('company_ateco_code') is-invalid @enderror"
                                           id="company_ateco_code" name="company_ateco_code" value="{{ old('company_ateco_code') }}"
                                           placeholder="{{ __('companies.ateco_code_placeholder') }}" maxlength="10">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>{{ __('companies.ateco_code_help') }}
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
                            <i class="fas fa-user-tie me-2"></i>{{ __('companies.company_manager_info') }}
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manager_name" class="form-label">
                                        {{ __('companies.first_name') }} <span class="text-danger">*</span>
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
                                    <label for="manager_surname" class="form-label">{{ __('companies.surname') }}</label>
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
                                        {{ __('companies.manager_email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('manager_email') is-invalid @enderror"
                                           id="manager_email" name="manager_email" value="{{ old('manager_email') }}" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>{{ __('companies.manager_email_help') }}
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
                        <strong>{{ __('companies.email_important') }}</strong> {{ __('companies.temp_password_notice') }}
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>{{ __('companies.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('companies.send_invitation') }}
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
                    <i class="fas fa-question-circle me-2"></i>{{ __('companies.what_happens_next') }}
                </h6>
            </div>
            <div class="card-body">
                <ol class="ps-3">
                    <li class="mb-2">{{ __('companies.step_create_account') }}</li>
                    <li class="mb-2">{{ __('companies.step_generate_password') }}</li>
                    <li class="mb-2">{{ __('companies.step_send_email') }}</li>
                    <li class="mb-2">{{ __('companies.step_receive_email') }}</li>
                    <li class="mb-2">{{ __('companies.step_login') }}</li>
                    <li class="mb-2">{{ __('companies.step_change_password') }}</li>
                    <li class="mb-2">{{ __('companies.step_manage_company') }}</li>
                </ol>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>{{ __('companies.security') }}
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>{{ __('companies.secure_token_generation') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>{{ __('companies.invitation_expires_48h') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>{{ __('companies.password_change_required') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>{{ __('companies.all_actions_logged') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
