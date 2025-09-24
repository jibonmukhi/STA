<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        if ($locale && $this->isValidLocale($locale)) {
            App::setLocale($locale);

            // Ensure the locale is stored in session for persistence
            if (!Session::has('locale')) {
                Session::put('locale', $locale);
            }
        }

        return $next($request);
    }

    /**
     * Get the locale for the current request
     */
    private function getLocale(Request $request): ?string
    {
        // Check if multi-language is enabled
        try {
            $multiLanguageEnabled = Setting::get('localization.multi_language_enabled', true);
            if (!$multiLanguageEnabled) {
                // Return the default application locale if multi-language is disabled
                return Setting::get('app.locale', config('app.locale', 'it'));
            }
        } catch (\Exception $e) {
            // If settings are not available, continue with normal logic
        }

        // 1. Check for URL parameter (for language switching)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if ($this->isValidLocale($locale)) {
                Session::put('locale', $locale);
                return $locale;
            }
        }

        // 2. Check session (user's chosen language)
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 3. Check user's preference (if authenticated)
        if ($request->user() && isset($request->user()->locale)) {
            return $request->user()->locale;
        }

        // 4. Fall back to application default first
        $defaultLocale = Setting::get('app.locale', config('app.locale', 'it'));
        if ($this->isValidLocale($defaultLocale)) {
            return $defaultLocale;
        }

        // 5. Check Accept-Language header as last resort
        $headerLocale = $this->getLocaleFromHeader($request);
        if ($headerLocale) {
            return $headerLocale;
        }

        // 6. Final fallback
        return 'it';
    }

    /**
     * Check if the locale is valid
     */
    private function isValidLocale(string $locale): bool
    {
        try {
            $availableLocales = Setting::get('app.available_locales', ['it', 'en']);
            $availableLocales = is_array($availableLocales) ? $availableLocales : ['it', 'en'];
            return in_array($locale, $availableLocales);
        } catch (\Exception $e) {
            // Fall back to supported locales if settings are not available
            $supportedLocales = ['it', 'en', 'es', 'fr', 'de', 'pt', 'nl', 'ru', 'zh', 'ja', 'ar'];
            return in_array($locale, $supportedLocales);
        }
    }

    /**
     * Get locale from Accept-Language header
     */
    private function getLocaleFromHeader(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        try {
            $availableLocales = Setting::get('app.available_locales', ['it', 'en']);
            $availableLocales = is_array($availableLocales) ? $availableLocales : ['it', 'en'];
        } catch (\Exception $e) {
            $availableLocales = ['it', 'en', 'es', 'fr', 'de', 'pt', 'nl', 'ru', 'zh', 'ja', 'ar'];
        }

        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';q=', trim($lang));
            $locale = trim($parts[0]);
            $quality = isset($parts[1]) ? (float) $parts[1] : 1.0;

            // Extract language code (e.g., 'it' from 'it-IT')
            $langCode = substr($locale, 0, 2);

            if (in_array($langCode, $availableLocales)) {
                $languages[$langCode] = $quality;
            }
        }

        if (empty($languages)) {
            return null;
        }

        // Sort by quality and return the best match
        arsort($languages);
        return array_key_first($languages);
    }
}
