<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\AuditLogService;

class SettingsController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('manage settings');

        $settings = [
            'localization' => [
                'app.locale' => Setting::get('app.locale', 'it'),
                'app.available_locales' => Setting::get('app.available_locales', ['it', 'en']),
                'app.timezone' => Setting::get('app.timezone', 'Europe/Rome'),
                'app.date_format' => Setting::get('app.date_format', 'd/m/Y'),
                'app.time_format' => Setting::get('app.time_format', 'H:i'),
                'app.currency' => Setting::get('app.currency', 'EUR'),
                'localization.allow_user_locale_change' => Setting::get('localization.allow_user_locale_change', true),
                'localization.multi_language_enabled' => Setting::get('localization.multi_language_enabled', true),
            ]
        ];

        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage settings');

        $supportedLocales = ['it', 'en', 'es', 'fr', 'de', 'pt', 'nl', 'ru', 'zh', 'ja', 'ar'];

        $request->validate([
            'app_locale' => 'required|string|in:' . implode(',', $supportedLocales),
            'app_available_locales' => 'required|array',
            'app_available_locales.*' => 'string|in:' . implode(',', $supportedLocales),
            'app_timezone' => 'required|string',
            'app_date_format' => 'required|string',
            'app_time_format' => 'required|string',
            'app_currency' => 'required|string|max:3',
            'allow_user_locale_change' => 'boolean',
            'multi_language_enabled' => 'boolean',
        ]);

        try {
            // Store old values for audit log
            $oldSettings = [
                'app.locale' => Setting::get('app.locale'),
                'app.available_locales' => Setting::get('app.available_locales'),
                'app.timezone' => Setting::get('app.timezone'),
                'app.date_format' => Setting::get('app.date_format'),
                'app.time_format' => Setting::get('app.time_format'),
                'app.currency' => Setting::get('app.currency'),
                'localization.allow_user_locale_change' => Setting::get('localization.allow_user_locale_change'),
                'localization.multi_language_enabled' => Setting::get('localization.multi_language_enabled'),
            ];

            Setting::set('app.locale', $request->app_locale, 'string', 'Default application locale', true);
            Setting::set('app.available_locales', $request->app_available_locales, 'json', 'Available application locales', true);
            Setting::set('app.timezone', $request->app_timezone, 'string', 'Default application timezone', true);
            Setting::set('app.date_format', $request->app_date_format, 'string', 'Default date format', true);
            Setting::set('app.time_format', $request->app_time_format, 'string', 'Default time format', true);
            Setting::set('app.currency', $request->app_currency, 'string', 'Default currency', true);
            Setting::set('localization.allow_user_locale_change', $request->boolean('allow_user_locale_change'), 'boolean', 'Allow users to change their locale preference', false);
            Setting::set('localization.multi_language_enabled', $request->boolean('multi_language_enabled'), 'boolean', 'Enable multi-language support', false);

            // Log settings update
            $newSettings = [
                'app.locale' => $request->app_locale,
                'app.available_locales' => $request->app_available_locales,
                'app.timezone' => $request->app_timezone,
                'app.date_format' => $request->app_date_format,
                'app.time_format' => $request->app_time_format,
                'app.currency' => $request->app_currency,
                'localization.allow_user_locale_change' => $request->boolean('allow_user_locale_change'),
                'localization.multi_language_enabled' => $request->boolean('multi_language_enabled'),
            ];

            AuditLogService::logCustom(
                'settings_updated',
                'System localization settings updated',
                'settings',
                'info',
                [
                    'old_settings' => $oldSettings,
                    'new_settings' => $newSettings,
                    'updated_by' => auth()->id()
                ]
            );

            return redirect()->route('settings.index')
                ->with('success', __('settings.settings_saved'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('settings.settings_error'))
                ->withInput();
        }
    }

    public function switchLanguage(Request $request)
    {
        $supportedLocales = ['it', 'en', 'es', 'fr', 'de', 'pt', 'nl', 'ru', 'zh', 'ja', 'ar'];

        $request->validate([
            'locale' => 'required|string|in:' . implode(',', $supportedLocales)
        ]);

        $availableLocales = Setting::get('app.available_locales', ['it', 'en']);

        if (!in_array($request->locale, $availableLocales)) {
            return redirect()->back()->with('error', __('common.error'));
        }

        Session::put('locale', $request->locale);
        App::setLocale($request->locale);

        return redirect()->back()->with('success', __('common.success'));
    }

    public function getPublicSettings()
    {
        return response()->json(Setting::getPublic());
    }
}
