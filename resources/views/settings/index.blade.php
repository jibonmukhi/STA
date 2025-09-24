@extends('layouts.advanced-dashboard')

@section('page-title', __('settings.settings'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Settings Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization" type="button" role="tab">
                                {{ __('settings.localization_settings') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                {{ __('settings.general_settings') }}
                            </button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('settings.store') }}">
                        @csrf

                        <div class="tab-content" id="settingsTabContent">
                            <!-- Localization Settings Tab -->
                            <div class="tab-pane fade show active" id="localization" role="tabpanel">
                                <h5 class="mb-4">{{ __('settings.localization_settings') }}</h5>

                                <div class="row g-3">
                                    <!-- Default Language -->
                                    <div class="col-md-6">
                                        <label for="app_locale" class="form-label">{{ __('settings.default_language') }}</label>
                                        <select id="app_locale" name="app_locale" class="form-select">
                                            <option value="it" {{ old('app_locale', $settings['localization']['app.locale']) === 'it' ? 'selected' : '' }}>
                                                {{ __('common.italian') }}
                                            </option>
                                            <option value="en" {{ old('app_locale', $settings['localization']['app.locale']) === 'en' ? 'selected' : '' }}>
                                                {{ __('common.english') }}
                                            </option>
                                        </select>
                                        @error('app_locale')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Available Languages -->
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('settings.available_languages') }}</label>
                                        <div class="mt-2">
                                            @php
                                                $availableLocales = old('app_available_locales', $settings['localization']['app.available_locales'] ?? ['it', 'en']);
                                                $availableLocales = is_array($availableLocales) ? $availableLocales : ['it', 'en'];
                                            @endphp
                                            <div class="form-check">
                                                <input type="checkbox" name="app_available_locales[]" value="it"
                                                       {{ in_array('it', $availableLocales) ? 'checked' : '' }}
                                                       class="form-check-input" id="lang_it">
                                                <label class="form-check-label" for="lang_it">{{ __('common.italian') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="app_available_locales[]" value="en"
                                                       {{ in_array('en', $availableLocales) ? 'checked' : '' }}
                                                       class="form-check-input" id="lang_en">
                                                <label class="form-check-label" for="lang_en">{{ __('common.english') }}</label>
                                            </div>
                                        </div>
                                        @error('app_available_locales')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Timezone -->
                                    <div class="col-md-6">
                                        <label for="app_timezone" class="form-label">{{ __('settings.timezone') }}</label>
                                        <select id="app_timezone" name="app_timezone" class="form-select">
                                            @php
                                                $currentTimezone = old('app_timezone', $settings['localization']['app.timezone']);
                                                $timezones = [
                                                    'Europe/Rome' => 'Europe/Rome (UTC+1)',
                                                    'Europe/London' => 'Europe/London (UTC+0)',
                                                    'America/New_York' => 'America/New_York (UTC-5)',
                                                    'America/Los_Angeles' => 'America/Los_Angeles (UTC-8)',
                                                ];
                                            @endphp
                                            @foreach($timezones as $value => $label)
                                                <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('app_timezone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Date Format -->
                                    <div class="col-md-6">
                                        <label for="app_date_format" class="form-label">{{ __('settings.date_format') }}</label>
                                        <select id="app_date_format" name="app_date_format" class="form-select">
                                            @php
                                                $currentDateFormat = old('app_date_format', $settings['localization']['app.date_format']);
                                                $dateFormats = [
                                                    'd/m/Y' => 'd/m/Y (31/12/2024)',
                                                    'm/d/Y' => 'm/d/Y (12/31/2024)',
                                                    'Y-m-d' => 'Y-m-d (2024-12-31)',
                                                    'd-m-Y' => 'd-m-Y (31-12-2024)',
                                                ];
                                            @endphp
                                            @foreach($dateFormats as $value => $label)
                                                <option value="{{ $value }}" {{ $currentDateFormat === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('app_date_format')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Time Format -->
                                    <div class="col-md-6">
                                        <label for="app_time_format" class="form-label">{{ __('settings.time_format') }}</label>
                                        <select id="app_time_format" name="app_time_format" class="form-select">
                                            @php
                                                $currentTimeFormat = old('app_time_format', $settings['localization']['app.time_format']);
                                                $timeFormats = [
                                                    'H:i' => 'H:i (24:00)',
                                                    'h:i A' => 'h:i A (12:00 PM)',
                                                ];
                                            @endphp
                                            @foreach($timeFormats as $value => $label)
                                                <option value="{{ $value }}" {{ $currentTimeFormat === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('app_time_format')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Currency -->
                                    <div class="col-md-6">
                                        <label for="app_currency" class="form-label">{{ __('settings.currency') }}</label>
                                        <select id="app_currency" name="app_currency" class="form-select">
                                            @php
                                                $currentCurrency = old('app_currency', $settings['localization']['app.currency']);
                                                $currencies = [
                                                    'EUR' => 'EUR (€)',
                                                    'USD' => 'USD ($)',
                                                    'GBP' => 'GBP (£)',
                                                ];
                                            @endphp
                                            @foreach($currencies as $value => $label)
                                                <option value="{{ $value }}" {{ $currentCurrency === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('app_currency')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Multi-Language Support -->
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold mb-3">{{ __('settings.multi_language_support') }}</h6>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="multi_language_enabled" value="1"
                                               {{ old('multi_language_enabled', $settings['localization']['localization.multi_language_enabled'] ?? true) ? 'checked' : '' }}
                                               class="form-check-input" id="multi_language_enabled">
                                        <label class="form-check-label" for="multi_language_enabled">
                                            {{ __('settings.enable_multi_language') }}
                                        </label>
                                        <div class="form-text">{{ __('settings.enable_multi_language_help') }}</div>
                                    </div>
                                    @error('multi_language_enabled')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror

                                    <div class="form-check" id="allow_user_change_container">
                                        <input type="checkbox" name="allow_user_locale_change" value="1"
                                               {{ old('allow_user_locale_change', $settings['localization']['localization.allow_user_locale_change'] ?? false) ? 'checked' : '' }}
                                               class="form-check-input" id="allow_user_locale_change">
                                        <label class="form-check-label" for="allow_user_locale_change">{{ __('Allow users to change language') }}</label>
                                    </div>
                                    @error('allow_user_locale_change')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>
                            </div>

                            <!-- General Settings Tab -->
                            <div class="tab-pane fade" id="general" role="tabpanel">
                                <h5 class="mb-4">{{ __('settings.general_settings') }}</h5>
                                <p class="text-muted">{{ __('Additional general settings will be added here') }}</p>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('settings.save_settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const multiLanguageCheckbox = document.getElementById('multi_language_enabled');
    const allowUserChangeContainer = document.getElementById('allow_user_change_container');
    const allowUserChangeCheckbox = document.getElementById('allow_user_locale_change');

    function toggleUserLanguageChange() {
        if (multiLanguageCheckbox.checked) {
            allowUserChangeContainer.style.opacity = '1';
            allowUserChangeCheckbox.disabled = false;
        } else {
            allowUserChangeContainer.style.opacity = '0.5';
            allowUserChangeCheckbox.disabled = true;
            allowUserChangeCheckbox.checked = false;
        }
    }

    // Initial state
    toggleUserLanguageChange();

    // Listen for changes
    multiLanguageCheckbox.addEventListener('change', toggleUserLanguageChange);
});
</script>
@endpush
