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
        Schema::create('session_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('course_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The student
            $table->foreignId('enrollment_id')->constrained('course_enrollments')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'excused', 'late'])->default('absent');
            $table->decimal('attended_hours', 5, 2)->default(0)->comment('Actual hours attended in this session');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null'); // Teacher who marked
            $table->timestamp('marked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['session_id', 'user_id'], 'session_user_unique');
            $table->index('enrollment_id');
            $table->index('session_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_attendances');
    }
};
