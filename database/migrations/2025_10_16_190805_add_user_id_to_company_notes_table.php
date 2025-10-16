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
        Schema::table('company_notes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('company_id')->constrained('users')->onDelete('cascade');
            $table->json('user_data')->nullable()->after('user_id'); // Store user details at time of note
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_notes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'user_data']);
        });
    }
};
