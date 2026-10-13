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
        // 1. Ubah struktur kolom di tabel angkatans
        Schema::table('angkatans', function (Blueprint $table) {
            if (Schema::hasColumn('angkatans', 'wali_kelas')) {
                $table->dropColumn('wali_kelas');
            }

            if (! Schema::hasColumn('angkatans', 'wali_kelas_id')) {
                $table->unsignedBigInteger('wali_kelas_id')->nullable()->after('is_active');
            }
        });

        if (! Schema::hasColumn('angkatans', 'wali_kelas_id')) {
            return;
        }

        // 2. Tambahkan foreign key constraint dengan pengaman tambahan untuk MySQL/PostgreSQL
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('angkatans', function (Blueprint $table) {
                // Drop foreign key terlebih dahulu jika sudah terlanjur ada untuk mencegah error 1826
                try {
                    $table->dropForeign(['wali_kelas_id']);
                } catch (\Exception $e) {
                    // Abaikan jika foreign key belum ada
                }

                $table->foreign('wali_kelas_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('angkatans', 'wali_kelas_id')) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                Schema::table('angkatans', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['wali_kelas_id']);
                    } catch (\Exception $e) {
                        // Abaikan jika foreign key tidak ada
                    }
                });
            }

            Schema::table('angkatans', function (Blueprint $table) {
                $table->dropColumn('wali_kelas_id');
            });
        }

        if (! Schema::hasColumn('angkatans', 'wali_kelas')) {
            Schema::table('angkatans', function (Blueprint $table) {
                $table->string('wali_kelas')->nullable()->after('is_active');
            });
        }
    }
};