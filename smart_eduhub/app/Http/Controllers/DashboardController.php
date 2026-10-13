<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Registration;
use App\Models\StudentPermit;
use App\Models\Point;
use App\Models\Module;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user?->isGuru()) {
            return redirect()->route('guru.dashboard');
        }

        if ($user?->isTeller()) {
            return redirect()->route('admin.finance.dashboard');
        }

        if ($user?->isBums()) {
            return redirect()->route('admin.finance.payments.history');
        }

        $viewYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);

        $studentQuery = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId);

        $attendanceQuery = Attendance::whereDate('date', now()->toDateString())->where('academic_year', $viewYear);
        $registrationQuery = Registration::where('status', 'pending')->where('academic_year', $viewYear);
        $permitQuery = StudentPermit::query();

        $totalStudents = $studentQuery->count();
        $todayAttendance = $attendanceQuery->count();
        $pendingRegistrations = $registrationQuery->count();
        $pendingPermitsCount = $permitQuery->count();
        
        $recentAttendances = Attendance::with('student')
            ->whereDate('date', now()->toDateString())
            ->where('academic_year', $viewYear);
        $recentAttendances = $recentAttendances->latest()->take(5)->get();

        $latestPermits = StudentPermit::with('student')->latest()->take(5)->get();
        
        $avgGpa = (clone $studentQuery)->avg('gpa') ?: 0.00;
        
        $studentsWithViolationsQuery = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*');

        $studentsWithViolations = $studentsWithViolationsQuery->with(['points'])->get()
            ->map(function($student) use ($viewYear) {
                $totalViolation = $student->points->where('type', 'violation')->where('academic_year', $viewYear)->sum('score');
                $totalAchievement = $student->points->where('type', 'achievement')->where('academic_year', $viewYear)->sum('score');
                $student->net_violation_points = max(0, $totalViolation - $totalAchievement);
                return $student;
            })
            ->filter(function($student) {
                return $student->net_violation_points >= 50;
            })
            ->sortByDesc('net_violation_points')
            ->take(5);

        // Creative Bank Data
        $totalCreativePoints = Point::where('type', 'achievement')->where('academic_year', $viewYear)->sum('score');
        $activeProjects = Module::count();

        return view('dashboard', compact(
            'totalStudents',
            'todayAttendance',
            'pendingRegistrations',
            'pendingPermitsCount',
            'recentAttendances',
            'latestPermits',
            'avgGpa',
            'studentsWithViolations',
            'totalCreativePoints',
            'activeProjects'
        ));
    }

    public function guruDashboard()
    {
        $user = auth()->user();

        if (!$user || !$user->isGuru()) {
            abort(403);
        }

        $courses = \App\Models\Course::with([
            'angkatan',
            'modules' => fn ($query) => $query->where('created_by', $user->id),
            'modules.questions'
        ])
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('modules', fn ($q) => $q->where('created_by', $user->id));
            })
            ->latest()
            ->get();

        $totalCourses = $courses->count();
        $totalModules = $courses->sum(fn ($course) => $course->modules->count());
        $totalQuestions = $courses->sum(fn ($course) => $course->modules->sum(fn ($module) => $module->questions->count()));

        $pendingGrades = \App\Models\AssignmentSubmission::whereIn(
            'module_id',
            $courses->flatMap(fn ($course) => $course->modules->pluck('id'))
        )
            ->where('status', 'submitted')
            ->count();

        return view('dashboard-guru', compact(
            'courses',
            'totalCourses',
            'totalModules',
            'totalQuestions',
            'pendingGrades'
        ));
    }
}
