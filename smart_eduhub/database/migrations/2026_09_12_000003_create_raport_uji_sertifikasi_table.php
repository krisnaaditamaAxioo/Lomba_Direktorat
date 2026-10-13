<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raport_uji_sertifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raport_id')->constrained('raport_industri')->onDelete('cascade');
            $table->tinyInteger('no');
            $table->string('nama_ujian');       // e.g. "UJI SERTIFIKASI ACP 1"
            $table->string('no_serti')->nullable();     // e.g. "A01444389"
            $table->string('status')->default('LULUS'); // LULUS / TIDAK LULUS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raport_uji_sertifikasi');
    }
};
