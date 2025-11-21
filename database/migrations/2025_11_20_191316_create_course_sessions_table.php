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
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('session_title'); // e.g., "Session 1", "Day 1 - Morning"
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('duration_hours', 5, 2)->nullable(); // Calculated from start/end time
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('max_participants')->nullable();
            $table->integer('session_order')->default(1); // Order of the session
            $table->timestamps();

            // Indexes for better performance
            $table->index('course_id');
            $table->index('session_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
