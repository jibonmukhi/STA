<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_vault_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('data_vault_categories')->onDelete('cascade');
            $table->string('code', 100)->comment('Value stored in database, e.g., beginner');
            $table->string('label_en', 255)->comment('English label');
            $table->string('label_it', 255)->comment('Italian label');
            $table->string('color', 50)->nullable()->comment('Color for badges, e.g., success, danger');
            $table->string('icon', 100)->nullable()->comment('Icon class, e.g., fas fa-star');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false)->comment('System items cannot be deleted');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable()->comment('Additional properties');
            $table->timestamps();

            $table->unique(['category_id', 'code']);
            $table->index('category_id');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_vault_items');
    }
};
