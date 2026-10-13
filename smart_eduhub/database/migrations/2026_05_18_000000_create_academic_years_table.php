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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 2025/2026
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Seed current year if exists in settings
        $current = DB::table('settings')->where('key', 'academic_year')->first();
        if ($current) {
            DB::table('academic_years')->insert([
                'name' => $current->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
