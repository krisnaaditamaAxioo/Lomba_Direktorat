<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom start_date dan end_date untuk tipe libur 'range'
        Schema::table('holidays', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('day_of_month');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Perbarui enum type agar mendukung 'range' (MySQL only — SQLite sudah diupdate di migration awal)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE holidays MODIFY COLUMN type ENUM('special', 'monthly', 'range') NOT NULL DEFAULT 'special'");
        }
    }

    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE holidays MODIFY COLUMN type ENUM('special', 'monthly') NOT NULL DEFAULT 'special'");
        }
    }
};
