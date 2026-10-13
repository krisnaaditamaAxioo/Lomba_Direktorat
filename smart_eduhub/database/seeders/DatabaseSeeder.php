<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
    
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $currentAcademicYear = '2026/2027';

        Setting::set('academic_year', $currentAcademicYear);

        AcademicYear::query()->update(['is_active' => false]);
        AcademicYear::updateOrCreate(
            ['name' => $currentAcademicYear],
            ['is_active' => true]
        );

        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@smartaxioo.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        $this->call(AngkatanSeeder::class);
        $this->call(StudentSeeder::class);
    }
}
    