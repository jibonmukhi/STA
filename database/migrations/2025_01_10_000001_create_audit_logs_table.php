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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // User information
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_role')->nullable();

            // Activity details
            $table->string('action'); // created, updated, deleted, logged_in, logged_out, etc.
            $table->string('model_type')->nullable(); // e.g., App\Models\User
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the affected model
            $table->string('model_name')->nullable(); // Display name for the model

            // Change details
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->json('changed_fields')->nullable(); // List of fields that were changed

            // Request information
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->text('url')->nullable();
            $table->string('route_name')->nullable();

            // Additional context
            $table->text('description')->nullable();
            $table->string('module')->nullable(); // users, courses, companies, etc.
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->json('metadata')->nullable(); // Any additional data

            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('action');
            $table->index('model_type');
            $table->index('model_id');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};