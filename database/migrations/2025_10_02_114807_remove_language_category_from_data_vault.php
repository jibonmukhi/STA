<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DataVaultCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete language category and its items (cascade delete will handle items)
        DataVaultCategory::where('code', 'language')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate language category if needed
        $category = DataVaultCategory::create([
            'code' => 'language',
            'name_en' => 'Language',
            'name_it' => 'Lingua',
            'description' => 'Available system languages',
            'is_system' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // Recreate language items
        $languages = [
            ['code' => 'en', 'label_en' => 'English', 'label_it' => 'Inglese', 'icon' => 'fas fa-flag-usa', 'sort_order' => 1],
            ['code' => 'it', 'label_en' => 'Italian', 'label_it' => 'Italiano', 'icon' => 'fas fa-flag', 'sort_order' => 2],
        ];

        foreach ($languages as $index => $lang) {
            \App\Models\DataVaultItem::create([
                'category_id' => $category->id,
                'code' => $lang['code'],
                'label_en' => $lang['label_en'],
                'label_it' => $lang['label_it'],
                'icon' => $lang['icon'],
                'is_system' => true,
                'is_active' => true,
                'is_default' => $lang['code'] === 'en',
                'sort_order' => $lang['sort_order'],
            ]);
        }
    }
};
