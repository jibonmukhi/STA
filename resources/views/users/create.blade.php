@extends('layouts.advanced-dashboard')

@section('page-title', __('users.create_new_user'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-user-plus me-2"></i>{{ __('users.create_new_user') }}
            </h4>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>{{ __('users.back_to_users') }}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>{{ __('users.user_information') }}
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
                        <strong>{{ __('users.please_fix_errors') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>{{ __('users.personal_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    {{ __('users.first_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="surname" class="form-label">{{ __('users.surname') }}</label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                       id="surname" name="surname" value="{{ old('surname') }}">
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender" class="form-label">{{ __('users.gender') }}</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">{{ __('users.select_gender') }}</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('users.male') }}</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('users.female') }}</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('users.other') }}</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">{{ __('users.date_of_birth') }}</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="place_of_birth" class="form-label">{{ __('users.place_of_birth') }}</label>
                                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                       id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}">
                                @error('place_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Personal Information -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country" class="form-label">{{ __('users.country') }}</label>
                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                    <option value="IT" {{ old('country', 'IT') == 'IT' ? 'selected' : '' }}>{{ __('users.italy') }}</option>
                                    <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>{{ __('users.united_states') }}</option>
                                    <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>{{ __('users.united_kingdom') }}</option>
                                    <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>{{ __('users.france') }}</option>
                                    <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>{{ __('users.germany') }}</option>
                                    <option value="ES" {{ old('country') == 'ES' ? 'selected' : '' }}>{{ __('users.spain') }}</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cf" class="form-label">{{ __('users.codice_fiscale') }}</label>
                                <input type="text" class="form-control @error('cf') is-invalid @enderror" 
                                       id="cf" name="cf" value="{{ old('cf') }}" maxlength="16" 
                                       placeholder="e.g. RSSMRA90A01H501X">
                                <div class="form-text">{{ __('users.italian_tax_code') }}</div>
                                @error('cf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="photo" class="form-label">{{ __('users.profile_photo') }}</label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                <div class="form-text">{{ __('users.max_file_size') }}</div>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Photo Preview -->
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div id="photo-preview" class="text-center d-none mb-3">
                                <img id="preview-image" src="" alt="Photo Preview" 
                                     class="img-fluid rounded-circle border" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-address-card me-2"></i>{{ __('users.contact_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    {{ __('users.email_address') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">{{ __('users.phone_number') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('users.address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Company Associations Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-building me-2"></i>{{ __('users.work_allocation') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <span><strong>{{ __('users.company_work_allocation') }}</strong></span>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addCompanyRow()">
                                            <i class="fas fa-plus me-1"></i>{{ __('users.add_company') }}
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Company Selection Row Template (Hidden) -->
                                        <div id="company-row-template" class="company-allocation-row d-none">
                                            <div class="row align-items-center mb-3 border-bottom pb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small">{{ __('users.company') }}</label>
                                                    <select class="form-select company-select" onchange="updateCompanySelection(this)" disabled>
                                                        <option value="">{{ __('users.select_company_placeholder') }}</option>
                                                        @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" data-name="{{ $company->name }}" data-email="{{ $company->email }}">
                                                            {{ $company->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <!-- Hidden input for form submission -->
                                                    <input type="hidden" class="company-input" name="companies[]" value="" disabled>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">{{ __('users.percentage') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control percentage-input" 
                                                               min="0" max="100" step="0.01" 
                                                               placeholder="0.00" 
                                                               disabled>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <!-- Hidden input for form submission -->
                                                    <input type="hidden" class="percentage-hidden" value="0" disabled>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">{{ __('users.primary') }}</label>
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input primary-radio" type="radio" 
                                                               name="primary_company" 
                                                               disabled>
                                                        <label class="form-check-label">
                                                            {{ __('users.primary') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">{{ __('users.current') }}</label>
                                                    <div class="pt-2">
                                                        <span class="badge bg-secondary company-badge">0%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">{{ __('users.action') }}</label>
                                                    <div class="pt-2">
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCompanyRow(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Company Rows Container -->
                                        <div id="company-rows-container">
                                            <!-- Rows will be added here dynamically -->
                                        </div>

                                        <!-- Empty State -->
                                        <div id="no-companies-message" class="text-center text-muted py-4">
                                            <i class="fas fa-building fa-2x mb-3 d-block"></i>
                                            <p>{{ __('users.no_companies_added') }}</p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('users.allocation_info') }}
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <strong>{{ __('users.total_percentage') }}: <span id="total-percentage" class="text-primary">0.00</span>%</strong>
                                                <div id="percentage-status" class="small">
                                                    <i class="fas fa-info-circle text-muted"></i> {{ __('users.no_allocation_set') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                @error('companies')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('company_percentages')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                @error('primary_company')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- System Information Section -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-cog me-2"></i>{{ __('users.system_information') }}
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    {{ __('users.password') }} <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    {{ __('users.confirm_password') }} <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('users.user_status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="parked" {{ old('status', 'parked') == 'parked' ? 'selected' : '' }}>{{ __('users.parked') }}</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('users.active') }}</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('users.inactive') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if($roles->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('users.user_roles') }}</label>
                                <div class="row">
                                    @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                                   value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ App\Models\User::formatRoleName($role->name) }}
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
                            <i class="fas fa-times me-2"></i>{{ __('users.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('users.create_user_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate initial total percentage
    calculateTotalPercentage();
    updateEmptyState();
    
    // Add form submission handler
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Clean up empty company inputs before submission
            const allCompanyInputs = document.querySelectorAll('.company-input');
            allCompanyInputs.forEach(input => {
                if (!input.value || input.value === '') {
                    // Disable empty inputs so they won't be submitted
                    input.disabled = true;
                }
            });
            
            // Also clean up percentage inputs for empty companies
            const allPercentageInputs = document.querySelectorAll('.percentage-hidden');
            allPercentageInputs.forEach(input => {
                const row = input.closest('.company-allocation-row');
                const companyInput = row.querySelector('.company-input');
                if (!companyInput || !companyInput.value || companyInput.value === '') {
                    input.disabled = true;
                }
            });
            
            // Check if we have any companies selected after cleanup
            const activeCompanies = document.querySelectorAll('.company-input:not([disabled])[value!=""]').length;
            
            if (activeCompanies === 0) {
                e.preventDefault();
                alert('{{ __('users.at_least_one_company') }}');
                return false;
            }
        });
    }
});

let companyRowCounter = 0;
let selectedCompanies = [];

function addCompanyRow() {
    const template = document.getElementById('company-row-template');
    const container = document.getElementById('company-rows-container');
    const newRow = template.cloneNode(true);
    
    // Remove template ID and show the row
    newRow.id = `company-row-${++companyRowCounter}`;
    newRow.classList.remove('d-none');
    
    // Update form control IDs to be unique
    const select = newRow.querySelector('.company-select');
    const percentageInput = newRow.querySelector('.percentage-input');
    const primaryRadio = newRow.querySelector('.primary-radio');
    const badge = newRow.querySelector('.company-badge');
    const companyHidden = newRow.querySelector('.company-input');
    const percentageHidden = newRow.querySelector('.percentage-hidden');
    
    select.id = `company_${companyRowCounter}`;
    select.disabled = false; // Enable the select
    
    percentageInput.id = `percentage_${companyRowCounter}`;
    
    // Enable hidden inputs for form submission
    companyHidden.disabled = false;
    percentageHidden.disabled = false;
    
    primaryRadio.value = companyRowCounter;
    primaryRadio.id = `primary_${companyRowCounter}`;
    
    badge.id = `badge_${companyRowCounter}`;
    
    // Set up the hidden inputs with proper names
    percentageHidden.name = `company_percentages[0]`; // Will be updated when company is selected
    
    // Add event listener to percentage input
    percentageInput.addEventListener('input', function() {
        calculateTotalPercentage();
    });
    
    // Add event listener to primary radio
    primaryRadio.addEventListener('change', function() {
        calculateTotalPercentage();
    });
    
    // Disable already selected companies
    updateAvailableCompanies(select);
    
    container.appendChild(newRow);
    updateEmptyState();
    
    // Focus on the company select
    select.focus();
}

function removeCompanyRow(button) {
    const row = button.closest('.company-allocation-row');
    const select = row.querySelector('.company-select');
    const selectedValue = select.value;
    
    // Remove from selected companies
    if (selectedValue) {
        selectedCompanies = selectedCompanies.filter(id => id !== selectedValue);
        updateAllCompanySelects();
    }
    
    row.remove();
    calculateTotalPercentage();
    updateEmptyState();
}

function updateCompanySelection(select) {
    const row = select.closest('.company-allocation-row');
    const percentageInput = row.querySelector('.percentage-input');
    const primaryRadio = row.querySelector('.primary-radio');
    const badge = row.querySelector('.company-badge');
    const companyHidden = row.querySelector('.company-input');
    const percentageHidden = row.querySelector('.percentage-hidden');
    const oldValue = select.getAttribute('data-old-value');
    
    // Remove old selection from selected companies
    if (oldValue) {
        selectedCompanies = selectedCompanies.filter(id => id !== oldValue);
    }
    
    if (select.value) {
        // Add new selection to selected companies
        selectedCompanies.push(select.value);
        
        // Update hidden inputs
        companyHidden.value = select.value;
        percentageHidden.name = `company_percentages[${select.value}]`;
        
        // Enable percentage input and primary radio
        percentageInput.disabled = false;
        primaryRadio.disabled = false;
        primaryRadio.value = select.value;
        
        // Update badge with company name
        const selectedOption = select.options[select.selectedIndex];
        badge.textContent = selectedOption.getAttribute('data-name');
        badge.className = 'badge bg-info';
        
        // Focus on percentage input
        percentageInput.focus();
    } else {
        // Clear hidden inputs
        companyHidden.value = '';
        percentageHidden.name = 'company_percentages[0]';
        percentageHidden.value = '0';
        
        // Disable inputs
        percentageInput.disabled = true;
        percentageInput.value = '';
        primaryRadio.disabled = true;
        primaryRadio.checked = false;
        badge.textContent = '0%';
        badge.className = 'badge bg-secondary';
    }
    
    // Store current value for next change
    select.setAttribute('data-old-value', select.value);
    
    // Update all company selects
    updateAllCompanySelects();
    calculateTotalPercentage();
}

function updateAllCompanySelects() {
    document.querySelectorAll('.company-select').forEach(select => {
        updateAvailableCompanies(select);
    });
}

function updateAvailableCompanies(targetSelect) {
    const currentValue = targetSelect.value;
    
    targetSelect.querySelectorAll('option').forEach(option => {
        if (option.value === '') {
            option.disabled = false;
            return;
        }
        
        // Disable if selected in another row, but not in current row
        option.disabled = selectedCompanies.includes(option.value) && option.value !== currentValue;
    });
}

function calculateTotalPercentage() {
    const percentageInputs = document.querySelectorAll('.percentage-input:not([disabled])');
    let total = 0;
    
    console.log('Calculating total percentage. Found ' + percentageInputs.length + ' active inputs');
    
    percentageInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
        console.log('Input value: ' + value + ', running total: ' + total);
        
        // Update individual badge and hidden input
        const row = input.closest('.company-allocation-row');
        if (row) {
            const badge = row.querySelector('.company-badge');
            const select = row.querySelector('.company-select');
            const percentageHidden = row.querySelector('.percentage-hidden');
            
            // Update hidden percentage value
            if (percentageHidden) {
                percentageHidden.value = value;
            }
            
            if (badge && select && select.value && value > 0) {
                badge.textContent = value + '%';
                badge.className = 'badge bg-info';
            } else if (badge && select && select.value) {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption) {
                    badge.textContent = selectedOption.getAttribute('data-name') || 'Selected';
                    badge.className = 'badge bg-warning';
                }
            }
        }
    });
    
    console.log('Final total: ' + total);
    const totalElement = document.getElementById('total-percentage');
    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
    updatePercentageStatus();
    
    return total;
}

function updatePercentageStatus() {
    const totalSpan = document.getElementById('total-percentage');
    const statusDiv = document.getElementById('percentage-status');
    
    if (!totalSpan || !statusDiv) {
        console.log('Total percentage elements not found');
        return;
    }
    
    const total = parseFloat(totalSpan.textContent) || 0;
    
    if (total === 100) {
        statusDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Perfect allocation!';
        totalSpan.className = 'text-success';
    } else if (total === 0) {
        statusDiv.innerHTML = '<i class="fas fa-info-circle text-muted"></i> No allocation set';
        totalSpan.className = 'text-muted';
    } else if (total < 100) {
        const remaining = 100 - total;
        statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> ${remaining.toFixed(2)}% remaining`;
        totalSpan.className = 'text-warning';
    } else {
        const excess = total - 100;
        statusDiv.innerHTML = `<i class="fas fa-times-circle text-danger"></i> ${excess.toFixed(2)}% over limit`;
        totalSpan.className = 'text-danger';
    }
}

function updateEmptyState() {
    const container = document.getElementById('company-rows-container');
    const emptyMessage = document.getElementById('no-companies-message');
    const hasRows = container.children.length > 0;
    
    emptyMessage.style.display = hasRows ? 'none' : 'block';
}


function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('photo-preview').classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection