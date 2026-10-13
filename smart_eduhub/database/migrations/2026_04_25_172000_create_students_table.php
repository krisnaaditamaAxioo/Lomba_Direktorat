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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('id_siswa')->unique();
            $table->string('username')->unique();
            $table->string('pin');
            $table->string('status')->default('active');
            $table->string('nama_lengkap');
            $table->string('angkatan')->nullable();
            $table->string('foto')->nullable();
            $table->string('nisn')->nullable();
            $table->string('no_nik')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('mother_name')->nullable();
            $table->string('religion')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('dateofbirth')->nullable();
            $table->text('address')->nullable();
            $table->string('nis_siswa')->nullable();
            $table->string('role')->default('student');
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('set null');
            $table->string('no_w_kelas')->nullable(); // Legacy field, can be derived from class_room relation
            
            // Old fields for compatibility (can be removed if not needed)
            $table->string('email')->nullable(); 
            $table->string('grade')->nullable();
            $table->decimal('gpa', 3, 2)->default(0.00);
            $table->string('previous_school')->nullable();
            $table->string('academic_year')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
