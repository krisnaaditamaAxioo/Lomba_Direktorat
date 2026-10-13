<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentPermit;
use App\Models\Setting;

class MarkAbsentStudents extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Mark students as absent if they have no attendance and no approved permit for the day';

    public function handle()
    {
        $this->info('Starting to mark absent students...');

        $today = now()->toDateString();
        $currentYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        // Check if today is a holiday
        $isHoliday = \App\Models\Holiday::checkHoliday($today);
        if ($isHoliday) {
            $this->info("Today ($today) is a holiday, skipping...");
            return;
        }

        $activeStudents = Student::where('status', 'active')
            ->whereHas('academicYears', function ($query) use ($currentYear) {
                $query->where('academic_years.name', $currentYear);
            })->get();

        $count = 0;

        foreach ($activeStudents as $student) {
            $hasAttendance = Attendance::where('student_id', $student->id)
                ->where('date', $today)
                ->where('academic_year', $currentYear)
                ->exists();

            if (!$hasAttendance) {
                $hasApprovedPermit = StudentPermit::where('student_id', $student->id)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($today) {
                        $query->where('start_date', '<=', $today)
                            ->where(function ($q) use ($today) {
                                $q->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $today);
                            });
                    })
                    ->exists();

                if (!$hasApprovedPermit) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'date' => $today,
                        'status' => 'absent',
                        'academic_year' => $currentYear,
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Marked {$count} students as absent for today ($today).");
    }
}

