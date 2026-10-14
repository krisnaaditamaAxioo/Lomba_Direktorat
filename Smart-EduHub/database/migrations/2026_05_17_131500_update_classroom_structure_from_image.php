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
        Schema::table('class_rooms', function (Blueprint $table) {
            // Rename level to grade based on image
            if (Schema::hasColumn('class_rooms', 'level')) {
                $table->renameColumn('level', 'grade');
            }
            
            // Add semester column (0 = Ganjil, 1 = Genap) based on image
            if (!Schema::hasColumn('class_rooms', 'semester')) {
                $table->integer('semester')->default(0)->after('name'); // 0=Ganjil, 1=Genap
            }

            // Rename wali_kelas_id to homeroom_teacher_id based on image
            // We need to drop foreign key first if it exists
            // Since we are moving to 'users' table for all staff, let's ensure the foreign key is correct
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->renameColumn('grade', 'level');
            $table->dropColumn('semester');
        });
    }
};
