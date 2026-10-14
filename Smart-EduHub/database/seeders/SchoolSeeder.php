<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Point;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'James Wilson',
                'password' => Hash::make('password'),
            ]
        );

        // Create Students
        $students = [
            [
                'student_id' => 'STU-2024-001',
                'name' => 'Sarah Chen',
                'email' => 'sarah.c@school.edu',
                'grade' => 'Grade 12-A',
                'status' => 'active',
                'gpa' => 3.85,
                'previous_school' => 'Oakwood Middle'
            ],
            [
                'student_id' => 'STU-2024-042',
                'name' => 'Marcus Thompson',
                'email' => 'm.thompson@school.edu',
                'grade' => 'Grade 11-B',
                'status' => 'active',
                'gpa' => 3.45,
                'previous_school' => 'Westside High'
            ],
            [
                'student_id' => 'STU-2024-118',
                'name' => 'Aisha Patel',
                'email' => 'a.patel@school.edu',
                'grade' => 'Grade 12-A',
                'status' => 'active',
                'gpa' => 3.92,
                'previous_school' => 'Central Academy'
            ]
        ];

        foreach ($students as $studentData) {
            $student = Student::create($studentData);

            // Create Attendance for each student
            Attendance::create([
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'status' => 'present',
                'check_in' => '07:30:00'
            ]);

            // Create Points for each student
            Point::create([
                'student_id' => $student->id,
                'type' => 'achievement',
                'score' => 10,
                'reason' => 'Excellent participation in class',
                'date' => now()->toDateString(),
                'academic_year' => \App\Models\Setting::get('academic_year', '2024-2025')
            ]);
        }

        // Create Registrations
        Registration::create([
            'application_id' => 'APP-2024-001',
            'name' => 'Jameson Sterling',
            'email' => 'j.sterling@example.com',
            'grade_applying' => 'Grade 10',
            'previous_school' => 'Greenwood Academy',
            'gpa' => 3.75,
            'status' => 'verified',
            'document_count' => 4
        ]);

        Registration::create([
            'application_id' => 'APP-2024-042',
            'name' => 'Mia Al-Fayed',
            'email' => 'm.alfayed@example.com',
            'grade_applying' => 'Grade 09',
            'previous_school' => 'East Side Middle',
            'gpa' => 3.60,
            'status' => 'pending',
            'document_count' => 2
        ]);
    }
}
