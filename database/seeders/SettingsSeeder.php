<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app.locale',
                'value' => 'it',
                'type' => 'string',
                'description' => 'Default application locale',
                'is_public' => true
            ],
            [
                'key' => 'app.available_locales',
                'value' => json_encode(['it', 'en']),
                'type' => 'json',
                'description' => 'Available application locales',
                'is_public' => true
            ],
            [
                'key' => 'app.timezone',
                'value' => 'Europe/Rome',
                'type' => 'string',
                'description' => 'Default application timezone',
                'is_public' => true
            ],
            [
                'key' => 'app.date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'description' => 'Default date format',
                'is_public' => true
            ],
            [
                'key' => 'app.time_format',
                'value' => 'H:i',
                'type' => 'string',
                'description' => 'Default time format',
                'is_public' => true
            ],
            [
                'key' => 'app.currency',
                'value' => 'EUR',
                'type' => 'string',
                'description' => 'Default currency',
                'is_public' => true
            ],
            [
                'key' => 'localization.allow_user_locale_change',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow users to change their locale preference',
                'is_public' => false
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
