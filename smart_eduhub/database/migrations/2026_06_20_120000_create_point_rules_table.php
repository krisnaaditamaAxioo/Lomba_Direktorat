<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama peraturan, misal: "Terlambat 5x"
            $table->string('condition_type'); // attendance_status, consecutive_days, total_monthly
            $table->string('attendance_status')->nullable(); // present, late, absent, excused
            $table->integer('threshold'); // Berapa kali untuk mencapai kondisi
            $table->string('point_type'); // achievement atau violation
            $table->integer('point_score'); // Berapa poin yang diberikan
            $table->string('reason_template'); // Template alasan, misal: "Terlambat {count} kali"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};
