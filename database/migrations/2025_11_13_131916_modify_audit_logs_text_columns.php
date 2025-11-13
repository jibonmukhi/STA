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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Change model_name to TEXT to accommodate large serialized data
            $table->text('model_name')->nullable()->change();

            // Also change other potentially large columns to TEXT
            $table->text('old_values')->nullable()->change();
            $table->text('new_values')->nullable()->change();
            $table->text('metadata')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Revert back to original VARCHAR type (if needed for rollback)
            // Note: This may truncate data if TEXT columns contain more than 255 chars
            $table->string('model_name')->nullable()->change();
            $table->string('old_values')->nullable()->change();
            $table->string('new_values')->nullable()->change();
            $table->string('metadata')->nullable()->change();
        });
    }
};
