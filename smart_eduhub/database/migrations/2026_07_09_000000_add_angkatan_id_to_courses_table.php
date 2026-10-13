<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Tambahkan pengecekan apakah kolom sudah ada untuk menghindari error
            if (!Schema::hasColumn('courses', 'angkatan_id')) {
                $table->foreignId('angkatan_id')->nullable()->constrained('angkatans')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'angkatan_id')) {
                // Drop foreign key terlebih dahulu jika ada
                $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('courses');
                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('angkatan_id', $foreignKey->getLocalColumns())) {
                        $table->dropForeign($foreignKey->getName());
                        break;
                    }
                }
                $table->dropColumn('angkatan_id');
            }
        });
    }
};
