<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('angkatans', function (Blueprint $table) {
            if (!Schema::hasColumn('angkatans', 'wali_kelas_id')) {
                $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            }
            if (Schema::hasColumn('angkatans', 'wali_kelas')) {
                $table->dropColumn('wali_kelas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('angkatans', function (Blueprint $table) {
            if (Schema::hasColumn('angkatans', 'wali_kelas_id')) {
                $table->dropForeign(['wali_kelas_id']);
                $table->dropColumn('wali_kelas_id');
            }
        });
    }
};
