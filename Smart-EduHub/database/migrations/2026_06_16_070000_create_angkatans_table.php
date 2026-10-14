<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('angkatans')) {
            Schema::create('angkatans', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $existingCodes = [];

        if (Schema::hasTable('students') && Schema::hasColumn('students', 'angkatan')) {
            $existingCodes = array_merge(
                $existingCodes,
                DB::table('students')
                    ->whereNotNull('angkatan')
                    ->distinct()
                    ->pluck('angkatan')
                    ->filter()
                    ->map(fn ($code) => trim((string) $code))
                    ->all()
            );
        }

        if (Schema::hasTable('registrations') && Schema::hasColumn('registrations', 'angkatan')) {
            $existingCodes = array_merge(
                $existingCodes,
                DB::table('registrations')
                    ->whereNotNull('angkatan')
                    ->distinct()
                    ->pluck('angkatan')
                    ->filter()
                    ->map(fn ($code) => trim((string) $code))
                    ->all()
            );
        }

        $codes = array_values(array_unique(array_filter(array_merge(
            ['1', '2', '3'],
            $existingCodes
        ))));

        sort($codes);

        foreach ($codes as $index => $code) {
            DB::table('angkatans')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => 'Angkatan ' . $code,
                    'sort_order' => is_numeric($code) ? (int) $code : $index + 1,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('angkatans');
    }
};
