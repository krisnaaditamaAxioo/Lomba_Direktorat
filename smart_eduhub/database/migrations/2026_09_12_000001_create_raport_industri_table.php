<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raport_industri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('angkatan_id')->constrained('angkatans')->onDelete('cascade');
            $table->string('tahun_pelajaran');          // e.g. "2025/2026_Genap"
            $table->string('tingkat')->nullable();       // e.g. "X (Sepuluh)"
            $table->string('program_keahlian')->nullable(); // e.g. "Rekayasa Perangkat Lunak"
            $table->string('trainer_name')->nullable();
            $table->string('trainer_no_serti')->nullable();
            $table->string('trainer_nbm')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Satu siswa hanya bisa punya 1 raport per tahun pelajaran
            $table->unique(['student_id', 'tahun_pelajaran'], 'unique_student_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raport_industri');
    }
};
