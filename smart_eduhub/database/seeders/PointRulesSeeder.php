<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointRule;

class PointRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PointRule::create([
            'name' => 'Terlambat 5x',
            'condition_type' => 'attendance_status',
            'attendance_status' => 'late',
            'threshold' => 5,
            'point_type' => 'violation',
            'point_score' => 5,
            'reason_template' => 'Terlambat {count} kali',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        PointRule::create([
            'name' => 'Terlambat 8x',
            'condition_type' => 'attendance_status',
            'attendance_status' => 'late',
            'threshold' => 8,
            'point_type' => 'violation',
            'point_score' => 10,
            'reason_template' => 'Terlambat {count} kali',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        PointRule::create([
            'name' => 'Alpa 3x',
            'condition_type' => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold' => 3,
            'point_type' => 'violation',
            'point_score' => 10,
            'reason_template' => 'Alpa {count} kali',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        PointRule::create([
            'name' => 'Alpa 6x',
            'condition_type' => 'attendance_status',
            'attendance_status' => 'absent',
            'threshold' => 6,
            'point_type' => 'violation',
            'point_score' => 20,
            'reason_template' => 'Alpa {count} kali',
            'sort_order' => 4,
            'is_active' => true,
        ]);
    }
}
