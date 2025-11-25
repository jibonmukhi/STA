@extends('layouts.advanced-dashboard')

@section('page-title', __('users.add_company_user'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ __('users.add_company_user') }}</h3>
                            <p class="card-text opacity-75 mb-0">{{ __('users.add_company_user_subtitle') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('users.back_to_users') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.flash-messages')

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ __('users.please_fix_errors') }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('company-users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>{{ __('users.personal_information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('users.first_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="surname" class="form-label">{{ __('users.surname') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror"
                                       id="surname" name="surname" value="{{ old('surname') }}" required>
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">{{ __('users.email_address') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">{{ __('users.username') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       id="username" name="username" value="{{ old('username') }}" maxlength="50"
                                       autocomplete="off" required readonly>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="use_email_as_username">
                                    <label class="form-check-label" for="use_email_as_username">
                                        {{ __('users.use_email_as_username') }}
                                    </label>
                                </div>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" id="edit_username_checkbox">
                                    <label class="form-check-label" for="edit_username_checkbox">
                                        {{ __('users.allow_edit_username') }}
                                    </label>
                                </div>
                                <div class="form-text">{{ __('users.username_hint') }}</div>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">{{ __('users.phone_number') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">
                                    {{ __('users.date_of_birth') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control datepicker @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth_display" name="date_of_birth_display" value="{{ old('date_of_birth') }}"
                                       placeholder="DD/MM/YYYY" autocomplete="off" required readonly>
                                <input type="hidden" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">
                                    {{ __('users.gender') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">{{ __('users.select_gender') }}</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('users.male') }}</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('users.female') }}</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>{{ __('users.other') }}</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">
                                    {{ __('users.place_of_birth') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror"
                                       id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}" required>
                                @error('place_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">
                                    {{ __('users.country') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                    <option value="">{{ __('users.select_company') }}</option>
                                    @foreach(dataVaultItems('country') as $item)
                                        <option value="{{ $item['code'] }}" {{ old('country', 'IT') === $item['code'] ? 'selected' : '' }}>
                                            {{ $item['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cf" class="form-label">
                                    {{ __('users.codice_fiscale') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('cf') is-invalid @enderror"
                                       id="cf" name="cf" value="{{ old('cf') }}" maxlength="16" placeholder="e.g. RSSMRA90A01H501X" required
                                       oninput="updateUsernameFromCF(this.value)">
                                @error('cf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">{{ __('users.address') }}</label>
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
                            <i class="fas fa-user-shield me-2"></i>{{ __('users.roles_permissions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">{{ __('users.system_role') }}</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                    <option value="">{{ __('users.select_role') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ App\Models\User::formatRoleName($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('users.role_help_text') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role_in_company" class="form-label">{{ __('users.role_in_company') }}</label>
                                <input type="text" class="form-control @error('role_in_company') is-invalid @enderror"
                                       id="role_in_company" name="role_in_company" value="{{ old('role_in_company', 'Employee') }}">
                                @error('role_in_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('users.role_in_company_hint') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Assignment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>{{ __('users.company_associations') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($companies->count() > 0)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('users.select_companies') }} <span class="text-danger">*</span></label>
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
                                    <label for="percentage" class="form-label">{{ __('users.ownership_percentage') }}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('percentage') is-invalid @enderror"
                                               id="percentage" name="percentage" value="{{ old('percentage', 0) }}"
                                               min="0" max="100" step="0.01">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('users.percentage_hint') }}</div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ __('common.info') }}:</strong> {{ __('users.parked_notice') }}
                                {{ __('users.default_password_notice') }} <strong>password123</strong> - {{ __('users.user_should_change') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>{{ __('users.no_companies_available') }}:</strong> {{ __('users.no_companies_message') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('company-users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> {{ __('users.cancel') }}
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning">
                                    <i class="fas fa-redo me-1"></i> {{ __('users.reset_form') }}
                                </button>
                                <button type="submit" class="btn btn-primary" {{ $companies->count() === 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-user-plus me-1"></i> {{ __('users.create_user_btn') }}
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
            alert('{{ __('users.select_at_least_one_company') }}');
            return false;
        }

        // Confirm user creation
        if (!confirm('{{ __('users.confirm_user_creation') }}')) {
            e.preventDefault();
            return false;
        }
    });

    // Update username from CF field
    function updateUsernameFromCF(cfValue) {
        const usernameField = document.getElementById('username');
        if (usernameField) {
            // Convert to uppercase and update username field
            usernameField.value = cfValue.toUpperCase();
        }
    }

    // Belfiore codes cache (loaded from Data Vault)
    let comuniCache = null;
    let countriesCache = null;

    // Load Italian Comuni codes
    async function loadComuni() {
        if (comuniCache) {
            return comuniCache;
        }

        try {
            const response = await fetch('/api/codice-fiscale/comuni');
            if (response.ok) {
                comuniCache = await response.json();
                return comuniCache;
            }
        } catch (error) {
            console.error('Error loading comuni:', error);
        }

        return [];
    }

    // Load Foreign Countries codes
    async function loadCountries() {
        if (countriesCache) {
            return countriesCache;
        }

        try {
            const response = await fetch('/api/codice-fiscale/countries');
            if (response.ok) {
                countriesCache = await response.json();
                return countriesCache;
            }
        } catch (error) {
            console.error('Error loading countries:', error);
        }

        return [];
    }

    // Get cadastral code for a place and country
    function getBelfioreCode(place, countryCode) {
        // For foreign countries, look up in countries cache
        if (countryCode !== 'IT') {
            if (countriesCache && countriesCache.length > 0) {
                // Try to find country by ISO code
                const countryMatch = countriesCache.find(item =>
                    item.iso_code === countryCode
                );
                if (countryMatch) {
                    return countryMatch.code;
                }
            }
            // Fallback for unknown countries
            return 'Z999';
        }

        // For Italian cities, try to match place name with comuni from cache
        if (comuniCache && comuniCache.length > 0) {
            const normalizedPlace = place.toUpperCase().trim();

            // Try exact match first - match against comune name
            const exactMatch = comuniCache.find(item => {
                const nome = (item.nome || '').toUpperCase().trim();
                return nome === normalizedPlace;
            });

            if (exactMatch) {
                return exactMatch.code;
            }

            // Try partial match - check if place name contains comune name or vice versa
            const partialMatch = comuniCache.find(item => {
                const nome = (item.nome || '').toUpperCase().trim();
                return nome.includes(normalizedPlace) || normalizedPlace.includes(nome);
            });

            if (partialMatch) {
                return partialMatch.code;
            }

            // Try matching with label (includes province code)
            const labelMatch = comuniCache.find(item => {
                const label = (item.label || '').toUpperCase().trim();
                return label.includes(normalizedPlace);
            });

            if (labelMatch) {
                return labelMatch.code;
            }
        }

        // Fallback: return placeholder
        console.warn('Could not find cadastral code for:', place, countryCode);
        return 'XXXX';
    }

    // Generate CF from personal information
    function generateCF() {
        const surname = document.getElementById('surname')?.value || '';
        const name = document.getElementById('name')?.value || '';
        const gender = document.getElementById('gender')?.value || '';
        const dateOfBirth = document.getElementById('date_of_birth_display')?.value || '';
        const placeOfBirth = document.getElementById('place_of_birth')?.value || '';
        const country = document.getElementById('country')?.value || 'IT';

        if (!surname && !name && !gender && !dateOfBirth && !placeOfBirth) {
            return; // No data to generate from
        }

        let cf = '';

        // Extract consonants and vowels
        function getConsonants(str) {
            return str.toUpperCase().replace(/[^BCDFGHJKLMNPQRSTVWXYZ]/g, '');
        }

        function getVowels(str) {
            return str.toUpperCase().replace(/[^AEIOU]/g, '');
        }

        function padString(str, length) {
            return (str + 'XXX').substring(0, length);
        }

        // 1. Surname (3 chars)
        if (surname) {
            const consonants = getConsonants(surname);
            const vowels = getVowels(surname);
            cf += padString(consonants + vowels, 3);
        } else {
            cf += 'XXX';
        }

        // 2. Name (3 chars) - Special rule: if 4+ consonants, skip the 2nd
        if (name) {
            const consonants = getConsonants(name);
            const vowels = getVowels(name);
            let nameCode;
            if (consonants.length >= 4) {
                // Take 1st, 3rd, 4th consonants
                nameCode = consonants.charAt(0) + consonants.charAt(2) + consonants.charAt(3);
            } else {
                nameCode = padString(consonants + vowels, 3);
            }
            console.log('Name encoding:', name, '-> Consonants:', consonants, ', Vowels:', vowels, ', Code:', nameCode);
            cf += nameCode;
        } else {
            cf += 'XXX';
        }

        // 3. Year of birth (2 chars)
        if (dateOfBirth) {
            // Parse date from dd/mm/yyyy format
            let parts, year, month, day;
            if (dateOfBirth.includes('/')) {
                // dd/mm/yyyy format
                parts = dateOfBirth.split('/');
                day = parseInt(parts[0], 10);
                month = parseInt(parts[1], 10);
                year = parts[2];
            } else if (dateOfBirth.includes('-')) {
                // yyyy-mm-dd format (fallback)
                parts = dateOfBirth.split('-');
                year = parts[0];
                month = parseInt(parts[1], 10);
                day = parseInt(parts[2], 10);
            }
            console.log('Date parsing:', dateOfBirth, '-> Year:', year, ', Month:', month, ', Day:', day);
            cf += year.substring(2); // Last 2 digits of year
        } else {
            cf += 'XX';
        }

        // 4. Month of birth (1 char) - Official month codes
        if (dateOfBirth) {
            // Parse month from date
            let month;
            if (dateOfBirth.includes('/')) {
                // dd/mm/yyyy format
                const parts = dateOfBirth.split('/');
                month = parseInt(parts[1], 10);
            } else if (dateOfBirth.includes('-')) {
                // yyyy-mm-dd format (fallback)
                const parts = dateOfBirth.split('-');
                month = parseInt(parts[1], 10);
            }
            const monthCodes = ['A', 'B', 'C', 'D', 'E', 'H', 'L', 'M', 'P', 'R', 'S', 'T'];
            const monthCode = monthCodes[month - 1]; // Array is 0-indexed
            console.log('Month encoding:', month, '-> Code:', monthCode);
            cf += monthCode;
        } else {
            cf += 'X';
        }

        // 5. Day of birth and gender (2 chars)
        if (dateOfBirth) {
            // Parse day from date
            let day;
            if (dateOfBirth.includes('/')) {
                // dd/mm/yyyy format
                const parts = dateOfBirth.split('/');
                day = parseInt(parts[0], 10);
            } else if (dateOfBirth.includes('-')) {
                // yyyy-mm-dd format (fallback)
                const parts = dateOfBirth.split('-');
                day = parseInt(parts[2], 10);
            }
            // For females, add 40 to day
            if (gender === 'female') {
                day += 40;
            }
            console.log('Day encoding:', day, '-> Adjusted day:', day, '(gender:', gender + ')');
            cf += day.toString().padStart(2, '0');
        } else {
            cf += 'XX';
        }

        // 6. Place code (4 chars) - Look up from Data Vault
        if (placeOfBirth && country) {
            // Get Belfiore code from data vault based on place and country
            const belfioreCode = getBelfioreCode(placeOfBirth, country);
            cf += belfioreCode;
        } else {
            cf += 'XXXX';
        }

        // 7. Check digit (1 char) - Calculate using official algorithm
        cf += calculateCheckDigit(cf);

        // Update CF field
        const cfField = document.getElementById('cf');
        if (cfField) {
            cfField.value = cf.substring(0, 16);
            // Also update username
            updateUsernameFromCF(cf.substring(0, 16));
        }
    }

    // Calculate check digit using official Italian algorithm
    function calculateCheckDigit(cf15) {
        // Odd position values (1st, 3rd, 5th, etc.)
        const oddValues = {
            '0': 1, '1': 0, '2': 5, '3': 7, '4': 9, '5': 13, '6': 15, '7': 17, '8': 19, '9': 21,
            'A': 1, 'B': 0, 'C': 5, 'D': 7, 'E': 9, 'F': 13, 'G': 15, 'H': 17, 'I': 19, 'J': 21,
            'K': 2, 'L': 4, 'M': 18, 'N': 20, 'O': 11, 'P': 3, 'Q': 6, 'R': 8, 'S': 12, 'T': 14,
            'U': 16, 'V': 10, 'W': 22, 'X': 25, 'Y': 24, 'Z': 23
        };

        // Even position values (2nd, 4th, 6th, etc.)
        const evenValues = {
            '0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9,
            'A': 0, 'B': 1, 'C': 2, 'D': 3, 'E': 4, 'F': 5, 'G': 6, 'H': 7, 'I': 8, 'J': 9,
            'K': 10, 'L': 11, 'M': 12, 'N': 13, 'O': 14, 'P': 15, 'Q': 16, 'R': 17, 'S': 18, 'T': 19,
            'U': 20, 'V': 21, 'W': 22, 'X': 23, 'Y': 24, 'Z': 25
        };

        // Remainder to check character mapping
        const checkChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        let sum = 0;
        for (let i = 0; i < 15; i++) {
            const char = cf15.charAt(i);
            if (i % 2 === 0) {
                // Odd position (0-indexed, so 0, 2, 4... are actually 1st, 3rd, 5th...)
                sum += oddValues[char] || 0;
            } else {
                // Even position
                sum += evenValues[char] || 0;
            }
        }

        return checkChars.charAt(sum % 26);
    }

    // Handle username readonly toggle and email copy
    const usernameField = document.getElementById('username');
    const editUsernameCheckbox = document.getElementById('edit_username_checkbox');
    const useEmailCheckbox = document.getElementById('use_email_as_username');
    const emailField = document.getElementById('email');

    // Handle "Use email as username" checkbox
    if (useEmailCheckbox && usernameField && emailField) {
        useEmailCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Copy full email address to username
                const emailValue = emailField.value.trim();
                if (emailValue) {
                    // Use full email address
                    usernameField.value = emailValue;
                }
                // Uncheck edit checkbox
                if (editUsernameCheckbox) {
                    editUsernameCheckbox.checked = false;
                }
                // Keep readonly
                usernameField.setAttribute('readonly', 'readonly');
            } else {
                // Restore CF value when unchecked
                const cfField = document.getElementById('cf');
                if (cfField && cfField.value) {
                    usernameField.value = cfField.value.toUpperCase();
                }
            }
        });

        // Update username when email changes (if checkbox is checked)
        emailField.addEventListener('input', function() {
            if (useEmailCheckbox.checked) {
                const emailValue = this.value.trim();
                if (emailValue) {
                    // Use full email address
                    usernameField.value = emailValue;
                }
            }
        });
    }

    // Handle "Allow editing username" checkbox
    if (editUsernameCheckbox && usernameField) {
        editUsernameCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck use email checkbox
                if (useEmailCheckbox) {
                    useEmailCheckbox.checked = false;
                }
                usernameField.removeAttribute('readonly');
                usernameField.focus();
            } else {
                usernameField.setAttribute('readonly', 'readonly');
            }
        });
    }

    // Load Belfiore codes for CF generation
    loadComuni();
    loadCountries();

    // Initialize jQuery UI Datepicker
    jQuery(function($) {
        $('#date_of_birth_display').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:' + new Date().getFullYear(),
            maxDate: new Date(),
            onSelect: function(dateText) {
                // Convert dd/mm/yyyy to yyyy-mm-dd for hidden field
                const parts = dateText.split('/');
                const isoDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                $('#date_of_birth').val(isoDate);
                // Trigger CF generation
                generateCF();
            }
        });

        // Make the field clickable to open datepicker
        $('#date_of_birth_display').on('click', function() {
            $(this).datepicker('show');
        });

        // Convert existing value from yyyy-mm-dd to dd/mm/yyyy if present
        const currentValue = $('#date_of_birth').val();
        if (currentValue && currentValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
            const parts = currentValue.split('-');
            $('#date_of_birth_display').val(parts[2] + '/' + parts[1] + '/' + parts[0]);
        }
    });

    // Attach event listeners to all relevant fields for CF generation
    const fieldsToWatch = ['surname', 'name', 'gender', 'date_of_birth_display', 'place_of_birth', 'country'];

    fieldsToWatch.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', generateCF);
            field.addEventListener('change', generateCF);
        }
    });
</script>
@endsection