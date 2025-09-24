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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('course_code')->unique();
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->string('category');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('duration_hours');
            $table->decimal('credits', 4, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->string('instructor')->nullable();
            $table->text('prerequisites')->nullable();
            $table->enum('delivery_method', ['online', 'offline', 'hybrid']);
            $table->integer('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(false);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
