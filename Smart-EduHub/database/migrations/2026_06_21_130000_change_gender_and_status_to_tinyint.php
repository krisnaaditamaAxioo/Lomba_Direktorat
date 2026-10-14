<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Ambil semua kolom untuk melihat tipe saat ini
        $columns = DB::select("SHOW COLUMNS FROM students WHERE Field IN ('gender', 'status')");
        
        // Tambahkan kolom temporary untuk menyimpan data lama
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender_temp')->nullable();
            $table->string('status_temp')->nullable();
        });
        
        // Salin data ke kolom temporary
        DB::statement("UPDATE students SET gender_temp = gender, status_temp = status");
        
        // Ubah kolom asli ke tinyint
        Schema::table('students', function (Blueprint $table) {
            $table->tinyInteger('gender')->default(0)->change();
            $table->tinyInteger('status')->default(0)->change();
        });
        
        // Update nilai dari kolom temporary
        DB::statement("UPDATE students SET gender = 0 WHERE gender_temp = 'L' OR gender_temp = '0'");
        DB::statement("UPDATE students SET gender = 1 WHERE gender_temp = 'P' OR gender_temp = '1'");
        
        DB::statement("UPDATE students SET status = 0 WHERE status_temp IN ('active', '0')");
        DB::statement("UPDATE students SET status = 1 WHERE status_temp IN ('inactive', 'on leave', '1')");
        
        // Hapus kolom temporary
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gender_temp', 'status_temp']);
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Tambahkan kolom temporary lagi
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender_temp')->nullable();
            $table->string('status_temp')->nullable();
        });
        
        // Salin data ke kolom temporary
        DB::statement("UPDATE students SET gender_temp = gender, status_temp = status");
        
        // Ubah kolom asli kembali
        Schema::table('students', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable()->change();
            $table->string('status')->default('active')->change();
        });
        
        // Update nilai dari kolom temporary
        DB::statement("UPDATE students SET gender = 'L' WHERE gender_temp = 0");
        DB::statement("UPDATE students SET gender = 'P' WHERE gender_temp = 1");
        
        DB::statement("UPDATE students SET status = 'active' WHERE status_temp = 0");
        DB::statement("UPDATE students SET status = 'inactive' WHERE status_temp = 1");
        
        // Hapus kolom temporary
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gender_temp', 'status_temp']);
        });
    }
};
