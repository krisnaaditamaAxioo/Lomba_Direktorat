<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Angkatan;
use App\Models\Student;
use App\Models\Setting;
use App\Models\AcademicYear;
use App\Models\AcademicYearStudent;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentAcademicYear = Setting::get('academic_year', '2026/2027');

        $academicYear = AcademicYear::updateOrCreate(
            ['name' => $currentAcademicYear],
            ['is_active' => true]
        );

        // Get or create default angkatan
        $angkatan = Angkatan::ordered()->first();
        if (!$angkatan) {
            $angkatan = Angkatan::create([
                'code' => '1',
                'name' => 'Angkatan 1',
                'is_active' => true,
            ]);
        }

        $student = Student::updateOrCreate(
            ['username' => 'siswa123'],
            [
                'id_siswa' => 'STU-1001',
                'pin' => '123456',
                'status' => 0, // 0 = Aktif
                'nama_lengkap' => 'Siswa Percobaan',
                'angkatan_id' => $angkatan->id,
                'nisn' => '0012345678',
                'gender' => 0, // 0 = Laki-laki
                'email' => 'siswa123@smartaxioo.test',
                'address' => 'Jl. Pendidikan No. 123',
            ]
        );

        AcademicYearStudent::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            []
        );
    }
}
