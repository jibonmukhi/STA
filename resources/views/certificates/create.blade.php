@extends('layouts.advanced-dashboard')

@section('title', __('certificates.create_certificate'))

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-section h5 {
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        transition: border-color 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #007bff;
    }

    .file-upload-area.dragover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('certificates.create_certificate') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('certificates.index') }}">{{ __('certificates.certificates') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('certificates.create_certificate') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information Section -->
        <div class="form-section">
            <h5 class="required-field">{{ __('certificates.basic_information') }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label required-field">{{ __('certificates.certificate_name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                        <div class="form-text">{{ __('certificates.certificate_name_help') }}</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="subject" class="form-label required-field">{{ __('certificates.subject') }}</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                               id="subject" name="subject" value="{{ old('subject') }}" required maxlength="255">
                        <div class="form-text">{{ __('certificates.subject_help') }}</div>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('certificates.description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" maxlength="1000">{{ old('description') }}</textarea>
                        <div class="form-text">{{ __('certificates.optional_field') }}</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="certificate_type" class="form-label required-field">{{ __('certificates.certificate_type') }}</label>
                        <select class="form-select @error('certificate_type') is-invalid @enderror"
                                id="certificate_type" name="certificate_type" required>
                            <option value="">{{ __('certificates.filter_by_type') }}</option>
                            @foreach($certificateTypes as $key => $value)
                                <option value="{{ $key }}" {{ old('certificate_type') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('certificate_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="level" class="form-label">{{ __('certificates.level') }}</label>
                        <select class="form-select @error('level') is-invalid @enderror"
                                id="level" name="level">
                            <option value="">{{ __('certificates.optional_field') }}</option>
                            @foreach($certificateLevels as $key => $value)
                                <option value="{{ $key }}" {{ old('level') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="language" class="form-label required-field">{{ __('certificates.language') }}</label>
                        <select class="form-select @error('language') is-invalid @enderror"
                                id="language" name="language" required>
                            <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>
                                {{ __('certificates.english') }}
                            </option>
                            <option value="it" {{ old('language') == 'it' ? 'selected' : '' }}>
                                {{ __('certificates.italian') }}
                            </option>
                        </select>
                        @error('language')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_id" class="form-label required-field">{{ __('certificates.user') }}</label>
                        <select class="form-select @error('user_id') is-invalid @enderror"
                                id="user_id" name="user_id" required>
                            <option value="">{{ __('certificates.certificate_holder') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_id" class="form-label">{{ __('certificates.company') }}</label>
                        <select class="form-select @error('company_id') is-invalid @enderror"
                                id="company_id" name="company_id">
                            <option value="">{{ __('certificates.optional_field') }}</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Details Section -->
        <div class="form-section">
            <h5 class="required-field">{{ __('certificates.training_details') }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="training_organization" class="form-label required-field">{{ __('certificates.training_organization') }}</label>
                        <input type="text" class="form-control @error('training_organization') is-invalid @enderror"
                               id="training_organization" name="training_organization" value="{{ old('training_organization') }}"
                               required maxlength="255">
                        <div class="form-text">{{ __('certificates.training_org_help') }}</div>
                        @error('training_organization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="training_organization_code" class="form-label">{{ __('certificates.training_organization_code') }}</label>
                        <input type="text" class="form-control @error('training_organization_code') is-invalid @enderror"
                               id="training_organization_code" name="training_organization_code"
                               value="{{ old('training_organization_code') }}" maxlength="100">
                        <div class="form-text">{{ __('certificates.optional_field') }}</div>
                        @error('training_organization_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="instructor_name" class="form-label">{{ __('certificates.instructor_name') }}</label>
                        <input type="text" class="form-control @error('instructor_name') is-invalid @enderror"
                               id="instructor_name" name="instructor_name" value="{{ old('instructor_name') }}" maxlength="255">
                        <div class="form-text">{{ __('certificates.optional_field') }}</div>
                        @error('instructor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="duration_months" class="form-label">{{ __('certificates.duration_months') }}</label>
                        <input type="number" class="form-control @error('duration_months') is-invalid @enderror"
                               id="duration_months" name="duration_months" value="{{ old('duration_months') }}"
                               min="1" max="120">
                        <div class="form-text">{{ __('certificates.optional_field') }}</div>
                        @error('duration_months')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="training_organization_address" class="form-label">{{ __('certificates.training_organization_address') }}</label>
                        <textarea class="form-control @error('training_organization_address') is-invalid @enderror"
                                  id="training_organization_address" name="training_organization_address"
                                  rows="2" maxlength="500">{{ old('training_organization_address') }}</textarea>
                        <div class="form-text">{{ __('certificates.optional_field') }}</div>
                        @error('training_organization_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Details Section -->
        <div class="form-section">
            <h5 class="required-field">{{ __('certificates.certificate_information') }}</h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="certificate_number" class="form-label">{{ __('certificates.certificate_number') }}</label>
                        <input type="text" class="form-control @error('certificate_number') is-invalid @enderror"
                               id="certificate_number" name="certificate_number" value="{{ old('certificate_number') }}"
                               maxlength="255">
                        <div class="form-text">{{ __('certificates.optional_field') }} - Auto-generated if empty</div>
                        @error('certificate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="issue_date" class="form-label required-field">{{ __('certificates.issue_date') }}</label>
                        <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                               id="issue_date" name="issue_date" value="{{ old('issue_date') }}" required>
                        @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="expiration_date" class="form-label required-field">{{ __('certificates.expiration_date') }}</label>
                        <input type="date" class="form-control @error('expiration_date') is-invalid @enderror"
                               id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}" required>
                        <div class="form-text">{{ __('certificates.expiration_after_issue') }}</div>
                        @error('expiration_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label required-field">{{ __('certificates.status') }}</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                {{ __('certificates.status_active') }}
                            </option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                {{ __('certificates.status_pending') }}
                            </option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                {{ __('certificates.status_suspended') }}
                            </option>
                            <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>
                                {{ __('certificates.status_expired') }}
                            </option>
                            <option value="revoked" {{ old('status') == 'revoked' ? 'selected' : '' }}>
                                {{ __('certificates.status_revoked') }}
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input @error('is_public') is-invalid @enderror"
                                   id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">
                                {{ __('certificates.is_public') }}
                            </label>
                            <div class="form-text">Make this certificate publicly visible for verification</div>
                        </div>
                        @error('is_public')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Results Section -->
        <div class="form-section">
            <h5>{{ __('certificates.assessment_results') }}</h5>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="hours_completed" class="form-label">{{ __('certificates.hours_completed') }}</label>
                        <input type="number" class="form-control @error('hours_completed') is-invalid @enderror"
                               id="hours_completed" name="hours_completed" value="{{ old('hours_completed') }}"
                               step="0.01" min="0" max="9999.99">
                        @error('hours_completed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="credits" class="form-label">{{ __('certificates.credits') }}</label>
                        <input type="number" class="form-control @error('credits') is-invalid @enderror"
                               id="credits" name="credits" value="{{ old('credits') }}"
                               step="0.01" min="0" max="999.99">
                        @error('credits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="score" class="form-label">{{ __('certificates.score') }}</label>
                        <input type="number" class="form-control @error('score') is-invalid @enderror"
                               id="score" name="score" value="{{ old('score') }}"
                               min="0" max="100">
                        <div class="form-text">0-100</div>
                        @error('score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="grade" class="form-label">{{ __('certificates.grade') }}</label>
                        <input type="text" class="form-control @error('grade') is-invalid @enderror"
                               id="grade" name="grade" value="{{ old('grade') }}" maxlength="50">
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance Information Section -->
        <div class="form-section">
            <h5>{{ __('certificates.compliance_info') }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="regulatory_body" class="form-label">{{ __('certificates.regulatory_body') }}</label>
                        <input type="text" class="form-control @error('regulatory_body') is-invalid @enderror"
                               id="regulatory_body" name="regulatory_body" value="{{ old('regulatory_body') }}" maxlength="255">
                        @error('regulatory_body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="compliance_standard" class="form-label">{{ __('certificates.compliance_standard') }}</label>
                        <input type="text" class="form-control @error('compliance_standard') is-invalid @enderror"
                               id="compliance_standard" name="compliance_standard" value="{{ old('compliance_standard') }}"
                               maxlength="255">
                        @error('compliance_standard')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Renewal Information Section -->
        <div class="form-section">
            <h5>{{ __('certificates.renewal_info') }}</h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('renewal_required') is-invalid @enderror"
                                   id="renewal_required" name="renewal_required" value="1"
                                   {{ old('renewal_required') ? 'checked' : '' }}>
                            <label class="form-check-label" for="renewal_required">
                                {{ __('certificates.renewal_required') }}
                            </label>
                        </div>
                        @error('renewal_required')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="renewal_period_months" class="form-label">{{ __('certificates.renewal_period_months') }}</label>
                        <input type="number" class="form-control @error('renewal_period_months') is-invalid @enderror"
                               id="renewal_period_months" name="renewal_period_months"
                               value="{{ old('renewal_period_months') }}" min="1" max="120">
                        <div class="form-text">Only applicable if renewal is required</div>
                        @error('renewal_period_months')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- File Attachments Section -->
        <div class="form-section">
            <h5>{{ __('certificates.file_attachments') }}</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="certificate_file" class="form-label">{{ __('certificates.certificate_file') }}</label>
                        <div class="file-upload-area">
                            <input type="file" class="form-control @error('certificate_file') is-invalid @enderror"
                                   id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text mt-2">{{ __('certificates.max_file_size_5mb') }}</div>
                        </div>
                        @error('certificate_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="transcript_file" class="form-label">{{ __('certificates.transcript_file') }}</label>
                        <div class="file-upload-area">
                            <input type="file" class="form-control @error('transcript_file') is-invalid @enderror"
                                   id="transcript_file" name="transcript_file" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text mt-2">{{ __('certificates.max_file_size_5mb') }}</div>
                        </div>
                        @error('transcript_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="form-section">
            <h5>{{ __('certificates.additional_info') }}</h5>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('certificates.notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="4" maxlength="2000">{{ old('notes') }}</textarea>
                        <div class="form-text">{{ __('certificates.optional_field') }} - Additional notes or comments</div>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-section">
            <div class="d-flex justify-content-between">
                <a href="{{ route('certificates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('certificates.back_to_certificates') }}
                </a>

                <div>
                    <button type="reset" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-undo me-1"></i>{{ __('certificates.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>{{ __('certificates.create_certificate_btn') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure certificate submenu is expanded
        const certificateSubmenu = document.getElementById('navigation-certificate-managementSubmenu');
        if (certificateSubmenu) {
            certificateSubmenu.classList.add('show');
            const parentToggle = certificateSubmenu.previousElementSibling?.querySelector('.nav-toggle');
            if (parentToggle) {
                parentToggle.classList.add('rotated');
            }
        }
        // Date validation
        const issueDate = document.getElementById('issue_date');
        const expirationDate = document.getElementById('expiration_date');

        function validateDates() {
            if (issueDate.value && expirationDate.value) {
                if (new Date(expirationDate.value) <= new Date(issueDate.value)) {
                    expirationDate.setCustomValidity('{{ __("certificates.expiration_after_issue") }}');
                } else {
                    expirationDate.setCustomValidity('');
                }
            }
        }

        issueDate.addEventListener('change', validateDates);
        expirationDate.addEventListener('change', validateDates);

        // Renewal period toggle
        const renewalRequired = document.getElementById('renewal_required');
        const renewalPeriod = document.getElementById('renewal_period_months');

        function toggleRenewalPeriod() {
            renewalPeriod.disabled = !renewalRequired.checked;
            if (!renewalRequired.checked) {
                renewalPeriod.value = '';
            }
        }

        renewalRequired.addEventListener('change', toggleRenewalPeriod);
        toggleRenewalPeriod(); // Initial state

        // File upload enhancements
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            const uploadArea = input.closest('.file-upload-area');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('dragover'), false);
            });

            uploadArea.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
        });
    });
</script>
@endpush