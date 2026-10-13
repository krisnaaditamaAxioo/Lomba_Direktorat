<?php

namespace App\Observers;

use App\Models\Point;
use App\Models\Letter;
use App\Models\Student;

class PointObserver
{
    /**
     * Handle the Point "created" event.
     */
    public function created(Point $point): void
    {
        $this->checkAndGenerateLetters($point);
    }

    /**
     * Handle the Point "updated" event.
     */
    public function updated(Point $point): void
    {
        $this->checkAndGenerateLetters($point);
    }

    private function checkAndGenerateLetters(Point $point)
    {
        // Only trigger for violations or if point changes affect the net score
        // For simplicity, we check whenever any point is created/updated for a student
        
        $studentId = $point->student_id;
        $academicYear = $point->academic_year;

        if (!$academicYear) return;

        $student = Student::find($studentId);
        if (!$student) return;

        $totalDemerit = Point::where('student_id', $studentId)
            ->where('type', 'violation')
            ->where('academic_year', $academicYear)
            ->sum('score');

        $totalAchievement = Point::where('student_id', $studentId)
            ->where('type', 'achievement')
            ->where('academic_year', $academicYear)
            ->sum('score');

        $currentViolationPoints = max(0, $totalDemerit - $totalAchievement);

        // Auto Letter Generation Logic
        $thresholds = [
            ['points' => 100, 'type' => 'sp3', 'label' => 'Surat Peringatan 3 (SP3)'],
            ['points' => 90,  'type' => 'sp2', 'label' => 'Surat Peringatan 2 (SP2)'],
            ['points' => 78,  'type' => 'sp1', 'label' => 'Surat Peringatan 1 (SP1)'],
            ['points' => 50,  'type' => 'binaan', 'label' => 'Surat Binaan'],
        ];

        foreach ($thresholds as $threshold) {
            if ($currentViolationPoints >= $threshold['points']) {
                // Check if this type of letter already exists for this student in this academic year
                $exists = Letter::where('student_id', $student->id)
                    ->where('type', $threshold['type'])
                    ->where('academic_year', $academicYear)
                    ->exists();

                if (!$exists) {
                    Letter::create([
                        'student_id' => $student->id,
                        'type' => $threshold['type'],
                        'points' => $currentViolationPoints,
                        'academic_year' => $academicYear,
                        'issued_at' => now(),
                        'reference_number' => date('Y/m') . '/' . strtoupper($threshold['type']) . '/' . $student->id . '/' . rand(100, 999)
                    ]);
                }
                // Break after the highest threshold reached to avoid multiple letters in one go
                break;
            }
        }
    }
}
