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
            $table->string('color', 20)->nullable()->after('course_code');
        });

        // Assign colors to existing courses
        $colors = ['primary', 'success', 'danger', 'warning', 'info', 'purple', 'pink', 'indigo', 'teal', 'orange'];
        $courses = DB::table('courses')->get();

        foreach ($courses as $index => $course) {
            DB::table('courses')
                ->where('id', $course->id)
                ->update(['color' => $colors[$index % count($colors)]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
