<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use Illuminate\Database\Seeder;

class AngkatanSeeder extends Seeder
{
    public function run(): void
    {
        $year = (int) now()->format('Y');

        $defaults = [
            ['code' => '1', 'name' => 'Angkatan 1', 'tahun_masuk' => $year - 2, 'sort_order' => 1, 'is_active' => true],
            ['code' => '2', 'name' => 'Angkatan 2', 'tahun_masuk' => $year - 1, 'sort_order' => 2, 'is_active' => true],
            ['code' => '3', 'name' => 'Angkatan 3', 'tahun_masuk' => $year, 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($defaults as $angkatan) {
            Angkatan::updateOrCreate(
                ['code' => $angkatan['code']],
                $angkatan
            );
        }
    }
}
