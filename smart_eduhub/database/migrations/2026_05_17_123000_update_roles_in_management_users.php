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
        Schema::table('management_users', function (Blueprint $table) {
            // Change enum to include new roles
            $table->string('role')->default('guru')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management_users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru', 'wali_kelas'])->default('guru')->change();
        });
    }
};
