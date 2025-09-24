<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Certificate;
use App\Policies\CertificatePolicy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Certificate::class, CertificatePolicy::class);

        // Set default locale from settings if available
        try {
            $defaultLocale = Setting::get('app.locale', config('app.locale', 'it'));
            if (is_string($defaultLocale)) {
                App::setLocale($defaultLocale);
            }

            // Share localization settings with all views
            $availableLocales = Setting::get('app.available_locales', ['it', 'en']);
            $availableLocales = is_array($availableLocales) ? $availableLocales : ['it', 'en'];
            View::share('availableLocales', $availableLocales);

            View::share('currentLocale', function () {
                return App::getLocale();
            });

            // Set timezone if available
            $timezone = Setting::get('app.timezone', 'Europe/Rome');
            if ($timezone && is_string($timezone)) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }

        } catch (\Exception $e) {
            // Settings table might not exist during migrations
            // Fall back to config values
            View::share('availableLocales', ['it', 'en']);
        }
    }
}
