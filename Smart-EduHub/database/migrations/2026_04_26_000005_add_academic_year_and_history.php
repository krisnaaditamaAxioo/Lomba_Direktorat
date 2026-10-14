<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add academic_year to attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('academic_year')->nullable()->after('status');
        });

        // Add academic_year to points
        Schema::table('points', function (Blueprint $table) {
            $table->string('academic_year')->nullable()->after('date');
        });

        // Create student_enrollments to track history
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

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
        
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('academic_year');
        });
    }
};
