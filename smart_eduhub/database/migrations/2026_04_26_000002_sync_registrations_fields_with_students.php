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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('nisn')->nullable()->after('phone');
            $table->string('no_nik')->nullable()->after('nisn');
            $table->enum('gender', ['L', 'P'])->nullable()->after('no_nik');
            $table->string('mother_name')->nullable()->after('gender');
            $table->string('religion')->nullable()->after('mother_name');
            $table->integer('height')->nullable()->after('religion');
            $table->integer('weight')->nullable()->after('height');
            $table->string('place_of_birth')->nullable()->after('weight');
            $table->date('dateofbirth')->nullable()->after('place_of_birth');
            $table->string('nis_siswa')->nullable()->after('dateofbirth');
            $table->string('grade')->nullable()->after('nis_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'nisn',
                'no_nik',
                'gender',
                'mother_name',
                'religion',
                'height',
                'weight',
                'place_of_birth',
                'dateofbirth',
                'nis_siswa',
                'grade'
            ]);
        });
    }
};
