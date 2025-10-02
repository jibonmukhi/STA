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
        Schema::create('data_vault_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('Unique identifier like course_category');
            $table->string('name_en', 255)->comment('English name');
            $table->string('name_it', 255)->comment('Italian name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false)->comment('System categories cannot be deleted');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_vault_categories');
    }
};
