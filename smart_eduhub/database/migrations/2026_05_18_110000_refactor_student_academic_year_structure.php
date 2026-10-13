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
        // Drop old table if exists
        Schema::dropIfExists('student_enrollments');

        // Create new table academic_year_student
        Schema::create('academic_year_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id'], 'student_academic_year_unique');
        });

        // Remove academic_year from students table
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_student');

        Schema::table('students', function (Blueprint $table) {
            $table->string('academic_year')->nullable();
        });

        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('academic_year');
            $table->string('grade');
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['student_id', 'academic_year']);
        });
    }
};
