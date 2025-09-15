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
        Schema::table('users', function (Blueprint $table) {
            // Add only the missing fields that don't exist yet
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('country', 2)->default('IT')->after('place_of_birth');
            $table->string('phone')->nullable()->after('country');
            $table->string('cf', 16)->nullable()->unique()->after('gender'); // Codice Fiscale (replacing tax_id_code)
            $table->string('photo')->nullable()->after('cf');
            
            // Modify existing status field to use enum instead of boolean
            $table->dropColumn('status');
        });
        
        // Add the enum status field separately
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'parked'])->default('active')->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'place_of_birth', 
                'country',
                'phone',
                'cf',
                'photo'
            ]);
            
            // Restore the original boolean status field
            $table->boolean('status')->default(true)->after('address');
        });
    }
};
