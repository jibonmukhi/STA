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
        // Delete gender category and its items (cascade delete will handle items)
        DataVaultCategory::where('code', 'gender')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate gender category if needed
        $category = DataVaultCategory::create([
            'code' => 'gender',
            'name_en' => 'Gender',
            'name_it' => 'Genere',
            'description' => 'Gender options',
            'is_system' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Recreate gender items
        $genders = [
            ['code' => 'male', 'label_en' => 'Male', 'label_it' => 'Maschio', 'icon' => 'fas fa-mars', 'sort_order' => 1],
            ['code' => 'female', 'label_en' => 'Female', 'label_it' => 'Femmina', 'icon' => 'fas fa-venus', 'sort_order' => 2],
            ['code' => 'other', 'label_en' => 'Other', 'label_it' => 'Altro', 'icon' => 'fas fa-genderless', 'sort_order' => 3],
        ];

        foreach ($genders as $gender) {
            \App\Models\DataVaultItem::create([
                'category_id' => $category->id,
                'code' => $gender['code'],
                'label_en' => $gender['label_en'],
                'label_it' => $gender['label_it'],
                'icon' => $gender['icon'],
                'is_system' => true,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => $gender['sort_order'],
            ]);
        }
    }
};
