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
            $table->string('surname')->nullable()->after('name');
            $table->string('mobile')->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('mobile');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('tax_id_code')->nullable()->unique()->after('date_of_birth');
            $table->boolean('status')->default(true)->after('tax_id_code');
            $table->text('address')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['surname', 'mobile', 'gender', 'date_of_birth', 'tax_id_code', 'status', 'address']);
        });
    }
};
