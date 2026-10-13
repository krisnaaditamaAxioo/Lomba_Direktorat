<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('angkatans') && ! Schema::hasColumn('angkatans', 'tahun_masuk')) {
            Schema::table('angkatans', function (Blueprint $table) {
                $table->unsignedSmallInteger('tahun_masuk')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('angkatans') && Schema::hasColumn('angkatans', 'tahun_masuk')) {
            Schema::table('angkatans', function (Blueprint $table) {
                $table->dropColumn('tahun_masuk');
            });
        }
    }
};
