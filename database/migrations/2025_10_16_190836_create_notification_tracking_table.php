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
        Schema::create('notification_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('notification_id'); // Laravel notification ID from notifications table
            $table->foreignId('recipient_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('company_note_id')->nullable()->constrained('company_notes')->onDelete('cascade');
            $table->string('notification_type'); // email, database, etc.
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->index('notification_id');
            $table->index(['recipient_user_id', 'status']);
            $table->index('company_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_tracking');
    }
};
