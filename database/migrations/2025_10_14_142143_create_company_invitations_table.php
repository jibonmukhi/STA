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
        Schema::create('company_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('company_name');
            $table->string('company_email')->unique();
            $table->string('company_phone')->nullable();
            $table->string('company_piva')->nullable();
            $table->string('company_ateco_code')->nullable();
            $table->string('manager_username');
            $table->string('manager_name');
            $table->string('manager_surname')->nullable();
            $table->string('manager_email');
            $table->string('temporary_password');
            $table->enum('status', ['pending', 'accepted', 'expired', 'rejected'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('token');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_invitations');
    }
};
