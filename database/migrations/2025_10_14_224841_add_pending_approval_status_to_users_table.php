<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'pending_approval' to the status enum
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'parked', 'pending_approval') NOT NULL DEFAULT 'parked'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any pending_approval users to parked
        DB::table('users')->where('status', 'pending_approval')->update(['status' => 'parked']);

        // Then remove the status from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'parked') NOT NULL DEFAULT 'parked'");
    }
};
