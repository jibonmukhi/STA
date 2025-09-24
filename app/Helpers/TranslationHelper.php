<?php

namespace App\Helpers;

class TranslationHelper
{
    /**
     * Safe translation function that ensures string output
     *
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $translation = __($key, $replace, $locale);

        // If translation returns an array, return the key or a fallback
        if (is_array($translation)) {
            // Try to extract the key part after the last dot
            $keyParts = explode('.', $key);
            return end($keyParts);
        }

        // Ensure we always return a string
        return (string) $translation;
    }

    /**
     * Get category name safely
     */
    public static function getCategoryName(string $category): string
    {
        $translation = trans('courses.categories.' . $category);
        return is_string($translation) ? $translation : ucfirst($category);
    }

    /**
     * Get level name safely
     */
    public static function getLevelName(string $level): string
    {
        $translation = trans('courses.levels.' . $level);
        return is_string($translation) ? $translation : ucfirst($level);
    }

    /**
     * Get delivery method name safely
     */
    public static function getDeliveryMethodName(string $method): string
    {
        $translation = trans('courses.delivery_methods.' . $method);
        return is_string($translation) ? $translation : ucfirst($method);
    }
}

// Global helper function
if (!function_exists('safe_trans')) {
    function safe_trans(string $key, array $replace = [], ?string $locale = null): string
    {
        return \App\Helpers\TranslationHelper::trans($key, $replace, $locale);
    }
}