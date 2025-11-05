@extends('layouts.advanced-dashboard')

@section('page-title', __('users.edit_user'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-user-edit me-2"></i>{{ __('users.edit_user') }}: {{ $user->full_name }}
            </h4>
            <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.show', $user) : route('users.index') }}" class="btn btn-outline-secondary">
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

                <form method="POST" action="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.update', $user) : route('users.update', $user) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="surname" class="form-label">
                                    {{ __('users.surname') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                       id="surname" name="surname" value="{{ old('surname', $user->surname) }}" required>
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender" class="form-label">
                                    Gender <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
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
                                <label for="date_of_birth" class="form-label">
                                    Date of Birth <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="place_of_birth" class="form-label">
                                    Place of Birth <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror"
                                       id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $user->place_of_birth) }}" required>
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
                                <label for="country" class="form-label">
                                    Country <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                    @foreach(dataVaultItems('country') as $item)
                                        <option value="{{ $item['code'] }}" {{ old('country', $user->country ?? 'IT') == $item['code'] ? 'selected' : '' }}>
                                            {{ $item['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cf" class="form-label">
                                    Codice Fiscale (CF) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('cf') is-invalid @enderror"
                                       id="cf" name="cf" value="{{ old('cf', $user->cf) }}" maxlength="16"
                                       placeholder="e.g. RSSMRA90A01H501X" required>
                                <div class="form-text">Italian tax identification code</div>
                                @error('cf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                <div class="form-text">Max file size: 2MB. Leave empty to keep current photo</div>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Photo Preview -->
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            @if($user->photo)
                                <div class="text-center mb-3">
                                    <img id="current-photo" src="{{ $user->photo_url }}" alt="{{ $user->full_name }}" 
                                         class="img-fluid rounded-circle border" style="max-height: 150px;">
                                    <p class="form-text mt-2">Current Photo</p>
                                </div>
                            @endif
                            <div id="photo-preview" class="text-center d-none mb-3">
                                <img id="preview-image" src="" alt="Photo Preview" 
                                     class="img-fluid rounded-circle border" style="max-height: 150px;">
                                <p class="form-text mt-2">New Photo Preview</p>
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
                                <div class="form-text">This serves as the username for login</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
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
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-building me-2"></i>Company Associations & Work Allocation
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <span><strong>Company Work Allocation</strong></span>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addCompanyRow()">
                                            <i class="fas fa-plus me-1"></i>Add Company
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Company Selection Row Template (Hidden) -->
                                        <div id="company-row-template" class="company-allocation-row d-none">
                                            <div class="row align-items-center mb-3 border-bottom pb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small">Company</label>
                                                    <select class="form-select company-select" onchange="updateCompanySelection(this)" disabled>
                                                        <option value="">Select Company...</option>
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
                                                    <label class="form-label small">Percentage</label>
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
                                                    <label class="form-label small">Primary</label>
                                                    <div class="form-check pt-2">
                                                        <input class="form-check-input primary-radio" type="radio" 
                                                               name="primary_company" 
                                                               disabled>
                                                        <label class="form-check-label">
                                                            Primary
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">Current</label>
                                                    <div class="pt-2">
                                                        <span class="badge bg-secondary company-badge">0%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small">Action</label>
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
                                            <!-- Existing companies will be loaded here -->
                                        </div>

                                        <!-- Empty State -->
                                        <div id="no-companies-message" class="text-center text-muted py-4">
                                            <i class="fas fa-building fa-2x mb-3 d-block"></i>
                                            <p>No companies assigned. Click "Add Company" to start.</p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Add companies and assign work percentages. Total must equal 100%.
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <strong>Total: <span id="total-percentage" class="text-primary">{{ $user->total_percentage }}</span>%</strong>
                                                <div id="percentage-status" class="small"></div>
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
                                <i class="fas fa-cog me-2"></i>System Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    New Password
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" autocomplete="new-password" value="">
                                <div class="form-text">Leave empty to keep current password</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Confirm New Password
                                </label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" autocomplete="new-password" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">User Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    @foreach(dataVaultItems('user_status') as $item)
                                        <option value="{{ $item['code'] }}" {{ old('status', $user->status) == $item['code'] ? 'selected' : '' }}>
                                            {{ $item['label'] }}
                                        </option>
                                    @endforeach
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
                                <label class="form-label">User Roles</label>
                                <div class="row">
                                    @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                                   value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
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
                        <a href="{{ isset($isCompanyManager) && $isCompanyManager ? route('company-users.show', $user) : route('users.index') }}" class="btn btn-secondary">
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
// Pre-load existing company data from server
const existingCompanies = @json($existingCompanies);

document.addEventListener('DOMContentLoaded', function() {
    // Load existing companies
    loadExistingCompanies();
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
                alert('Please add at least one company before submitting.');
                return false;
            }
        });
    }
});

let companyRowCounter = 0;
let selectedCompanies = [];

function loadExistingCompanies() {
    existingCompanies.forEach(company => {
        addCompanyRowWithData(company);
    });
    // Recalculate total after loading all companies
    setTimeout(function() {
        calculateTotalPercentage();
    }, 100);
}

function addCompanyRowWithData(companyData = null) {
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
    
    // If we have existing data, populate it
    if (companyData) {
        select.value = companyData.id;
        percentageInput.value = companyData.percentage;
        percentageInput.disabled = false;
        primaryRadio.disabled = false;
        primaryRadio.value = companyData.id;
        primaryRadio.checked = companyData.is_primary;
        
        // Update hidden inputs
        companyHidden.value = companyData.id;
        percentageHidden.name = `company_percentages[${companyData.id}]`;
        percentageHidden.value = companyData.percentage;
        
        // Update badge
        badge.textContent = companyData.percentage + '%';
        badge.className = 'badge bg-info';
        
        // Add to selected companies
        selectedCompanies.push(companyData.id.toString());
        select.setAttribute('data-old-value', companyData.id);
    } else {
        // Set up the hidden inputs with default values
        percentageHidden.name = `company_percentages[0]`;
    }
    
    // Add event listener to percentage input
    percentageInput.addEventListener('input', function() {
        calculateTotalPercentage();
    });
    
    // Add event listener to primary radio
    primaryRadio.addEventListener('change', function() {
        calculateTotalPercentage();
    });
    
    // Update available companies
    updateAvailableCompanies(select);
    
    container.appendChild(newRow);
    
    // Focus on the company select if it's a new row
    if (!companyData) {
        select.focus();
    }
}

function addCompanyRow() {
    addCompanyRowWithData();
    updateEmptyState();
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
    
    percentageInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
        
        // Update individual badge and hidden input
        const row = input.closest('.company-allocation-row');
        if (!row) {
            console.log('Could not find company row for percentage input');
            return;
        }
        
        const badge = row.querySelector('.company-badge');
        const select = row.querySelector('.company-select');
        const percentageHidden = row.querySelector('.percentage-hidden');
        
        // Update hidden percentage value
        if (percentageHidden) {
            percentageHidden.value = value;
        }
        
        if (select && badge && select.value && value > 0) {
            badge.textContent = value + '%';
            badge.className = 'badge bg-info';
        } else if (select && badge && select.value) {
            const selectedOption = select.options[select.selectedIndex];
            badge.textContent = selectedOption.getAttribute('data-name');
            badge.className = 'badge bg-warning';
        }
    });
    
    // Update total display with null checking
    const totalSpan = document.getElementById('total-percentage');
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2);
    } else {
        console.log('Total percentage span not found');
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
            // Hide current photo when previewing new one
            const currentPhoto = document.getElementById('current-photo');
            if (currentPhoto) {
                currentPhoto.parentElement.classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
