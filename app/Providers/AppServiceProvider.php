<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Certificate;
use App\Models\Course;
use App\Observers\CourseObserver;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;

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
        // Register observers
        Course::observe(CourseObserver::class);

        // Register policies
        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);

        // Log all email sending attempts
        Event::listen(MessageSending::class, function (MessageSending $event) {
            Log::info('Mail Sending Event', [
                'to' => collect($event->message->getTo())->keys()->toArray(),
                'subject' => $event->message->getSubject(),
            ]);
        });

        Event::listen(MessageSent::class, function (MessageSent $event) {
            Log::info('Mail Sent Successfully', [
                'to' => collect($event->message->getTo())->keys()->toArray(),
                'subject' => $event->message->getSubject(),
            ]);
        });

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
