<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raport_capaian_pembelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raport_id')->constrained('raport_industri')->onDelete('cascade');
            $table->tinyInteger('no');
            $table->string('aspek_penilaian');
            $table->string('predikat', 2)->nullable(); // A, B, C, D
            $table->string('keterangan')->nullable();  // Baik, Cukup, Kurang, dst.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raport_capaian_pembelajaran');
    }
};
