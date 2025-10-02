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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'dropped', 'failed'])->default('enrolled');
            $table->date('enrolled_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate enrollments
            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
