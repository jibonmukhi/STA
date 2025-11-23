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
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->decimal('attended_hours', 6, 2)->default(0)->after('progress_percentage')->comment('Total hours attended based on session attendance');
            $table->decimal('total_required_hours', 6, 2)->nullable()->after('attended_hours')->comment('Total hours required from all course sessions');
            $table->decimal('attendance_percentage', 5, 2)->default(0)->after('total_required_hours')->comment('Percentage of sessions attended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropColumn(['attended_hours', 'total_required_hours', 'attendance_percentage']);
        });
    }
};
