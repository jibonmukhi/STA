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
                                       autocomplete="off" required>
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
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
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

    // Generate CF from personal information
    function generateCF() {
        const surname = document.getElementById('surname')?.value || '';
        const name = document.getElementById('name')?.value || '';
        const gender = document.getElementById('gender')?.value || '';
        const dateOfBirth = document.getElementById('date_of_birth')?.value || '';
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
            if (consonants.length >= 4) {
                // Take 1st, 3rd, 4th consonants
                cf += consonants.charAt(0) + consonants.charAt(2) + consonants.charAt(3);
            } else {
                cf += padString(consonants + vowels, 3);
            }
        } else {
            cf += 'XXX';
        }

        // 3. Year of birth (2 chars)
        if (dateOfBirth) {
            const date = new Date(dateOfBirth);
            const year = date.getFullYear().toString().substring(2);
            cf += year;
        } else {
            cf += 'XX';
        }

        // 4. Month of birth (1 char) - Official month codes
        if (dateOfBirth) {
            const date = new Date(dateOfBirth);
            const monthCodes = ['A', 'B', 'C', 'D', 'E', 'H', 'L', 'M', 'P', 'R', 'S', 'T'];
            cf += monthCodes[date.getMonth()];
        } else {
            cf += 'X';
        }

        // 5. Day of birth and gender (2 chars)
        if (dateOfBirth) {
            const date = new Date(dateOfBirth);
            let day = date.getDate();
            // For females, add 40 to day
            if (gender === 'female') {
                day += 40;
            }
            cf += day.toString().padStart(2, '0');
        } else {
            cf += 'XX';
        }

        // 6. Place code (4 chars)
        if (placeOfBirth && country) {
            // For foreign countries, use special codes starting with Z
            // Official Belfiore codes for foreign countries
            const foreignCountryCodes = {
                'US': 'Z404', 'GB': 'Z114', 'FR': 'Z110', 'DE': 'Z112', 'ES': 'Z131', 'CH': 'Z133',
                'AR': 'Z600', 'BR': 'Z602', 'CA': 'Z401', 'AU': 'Z700', 'NZ': 'Z719',
                'CN': 'Z210', 'IN': 'Z222', 'JP': 'Z219', 'KR': 'Z230', 'RU': 'Z154',
                'AL': 'Z100', 'AT': 'Z102', 'BE': 'Z103', 'NL': 'Z126', 'GR': 'Z115',
                'PL': 'Z127', 'PT': 'Z128', 'RO': 'Z129', 'SE': 'Z132', 'TR': 'Z134',
                'EG': 'Z336', 'MA': 'Z330', 'ZA': 'Z359', 'AE': 'Z255', 'IL': 'Z219',
                'MX': 'Z514', 'VE': 'Z523', 'CL': 'Z506', 'CO': 'Z508',
            };

            if (country !== 'IT' && foreignCountryCodes[country]) {
                cf += foreignCountryCodes[country];
            } else if (country !== 'IT') {
                // Generic foreign place code
                cf += 'Z999';
            } else {
                // Italian cities - simplified placeholder
                // In real implementation, this would look up the Belfiore code
                const place = placeOfBirth.toUpperCase().replace(/[^A-Z0-9]/g, '');
                cf += padString(place, 4).substring(0, 4);
            }
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

    // Attach event listeners to all relevant fields
    const fieldsToWatch = ['surname', 'name', 'gender', 'date_of_birth', 'place_of_birth', 'country'];

    fieldsToWatch.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', generateCF);
            field.addEventListener('change', generateCF);
        }
    });
</script>
@endsection