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
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('parent_course_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('courses')
                  ->onDelete('cascade')
                  ->comment('NULL = master course (template), NOT NULL = course instance');

            $table->index('parent_course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['parent_course_id']);
            $table->dropIndex(['parent_course_id']);
            $table->dropColumn('parent_course_id');
        });
    }
};
