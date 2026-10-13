<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use Illuminate\Http\Request;

use App\Models\Attendance;
use App\Models\Student;

class AttendanceController extends Controller
{
    private function getViewYear()
    {
        return session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
    }

    public function scan()
    {
        $today = date('Y-m-d');
        $viewYear = $this->getViewYear();
        $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        
        $isHoliday = \App\Models\Holiday::checkHoliday($today);
        $holidayName = \App\Models\Holiday::getHolidayName($today);
        
        $stats = [
            'total_scans' => Attendance::where('date', $today)
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'on_time' => Attendance::where('date', $today)->where('status', 'present')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'late' => Attendance::where('date', $today)->where('status', 'late')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
        ];

        $recentLogs = Attendance::with(['student'])
            ->where('date', $today)
            ->where(function($query) use ($viewYear, $currentYear) {
                $query->where('academic_year', $viewYear);
                if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('attendance.scan', compact('stats', 'recentLogs', 'isHoliday', 'holidayName'));
    }

    private function checkAndApplyAttendancePenalties($student, $today, $academicYear)
    {
        $startOfMonth = \Carbon\Carbon::parse($today)->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($today)->endOfMonth()->format('Y-m-d');
        $monthName = \Carbon\Carbon::parse($today)->format('F Y');

        $rules = \App\Models\PointRule::active()->get();

        foreach ($rules as $rule) {
            if ($rule->condition_type === 'attendance_status') {
                // Hitung jumlah status kehadiran dalam bulan ini
                $count = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->where('status', $rule->attendance_status)
                    ->count();

                // Cek apakah sudah mencapai threshold dan belum diberi poin untuk rule ini di bulan ini
                if ($count >= $rule->threshold) {
                    $reason = str_replace('{count}', $count, $rule->reason_template);
                    $alreadyGiven = \App\Models\Point::where('student_id', $student->id)
                        ->where('reason', 'like', '%' . $reason . '%')
                        ->whereBetween('date', [$startOfMonth, $endOfMonth])
                        ->exists();

                    if (!$alreadyGiven) {
                        \App\Models\Point::create([
                            'student_id' => $student->id,
                            'type' => $rule->point_type,
                            'score' => $rule->point_score,
                            'reason' => $reason . ' (' . $monthName . ')',
                            'date' => $today,
                            'academic_year' => $academicYear
                        ]);
                    }
                }
            }
        }
    }

    private function checkAndRewardConsistentAttendance($student, $today, $academicYear)
    {
        // Placeholder method for future implementation
        // Logic: Reward for consistent attendance can be added here
        return;
    }

    public function processScan(Request $request)
    {
        $scanCode = trim($request->input('id_siswa'));
        
        // Search by multiple possible identifiers
        $student = Student::where('id_siswa', $scanCode)
            ->orWhere('nis_siswa', $scanCode)
            ->orWhere('nisn', $scanCode)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false, 
                'message' => 'Siswa tidak ditemukan dengan ID: ' . $scanCode
            ], 404);
        }

        if ($student->status !== 0 && $student->status !== '0') {
            return response()->json([
                'success' => false, 
                'message' => 'Status siswa ' . $student->nama_lengkap . ' tidak aktif (' . $student->status . ').'
            ], 403);
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');
        $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        // Check if today is a holiday
        $isHoliday = \App\Models\Holiday::checkHoliday($today);
        if ($isHoliday) {
            $holidayName = \App\Models\Holiday::getHolidayName($today);
            return response()->json([
                'success' => false, 
                'message' => 'Hari ini adalah hari libur (' . $holidayName . '), tidak bisa melakukan absensi.'
            ], 403);
        }

        // Get dynamic check-in time from settings
        $checkInSetting = \App\Models\Setting::get('check_in_time', '08:00');
        $checkInLimit = date('H:i:s', strtotime($checkInSetting));

        // Check if already attended today in current academic year
        $existing = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->where('academic_year', $currentYear)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false, 
                'message' => $student->nama_lengkap . ' sudah melakukan absensi hari ini (' . ($existing->check_in ?? 'Status: ' . $existing->status) . ').'
            ], 400);
        }

        // Determine status based on dynamic setting
        $status = $now > $checkInLimit ? 'late' : 'present';

        Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'check_in' => $now,
            'status' => $status,
            'academic_year' => $currentYear,
            'location' => 'Main Gate (Scanner)'
        ]);

        // Reward logic: -20 points if no late arrival for 4 months
        $this->checkAndRewardConsistentAttendance($student, $today, $currentYear);
        
        // Penalty logic: +8 points if late > 10 times or absent 5 times in a month
        $this->checkAndApplyAttendancePenalties($student, $today, $currentYear);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil! Selamat belajar, ' . $student->nama_lengkap . '.',
            'student' => $student->nama_lengkap,
            'time' => date('H:i A', strtotime($now)),
            'status' => $status
        ]);
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        $angkatanItems = Angkatan::active()->ordered()->get();
        
        $attendances = Attendance::with(['student'])
            ->where('date', $date)
            ->where(function($query) use ($viewYear, $currentYear) {
                $query->where('academic_year', $viewYear);
                if ($viewYear == $currentYear) {
                    $query->orWhereNull('academic_year');
                }
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
        
        $totalEnrollment = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId);
        
        $stats = [
            'total_enrollment' => $totalEnrollment->count(),
            'present_today' => Attendance::where('date', $date)->whereIn('status', ['present', 'late'])
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'on_time_today' => Attendance::where('date', $date)->where('status', 'present')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'late_arrival' => Attendance::where('date', $date)->where('status', 'late')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'absent_today' => Attendance::where('date', $date)->where('status', 'absent')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
            'excused_today' => Attendance::where('date', $date)->where('status', 'excused')
                ->where(function($query) use ($viewYear, $currentYear) {
                    $query->where('academic_year', $viewYear);
                    if ($viewYear == $currentYear) $query->orWhereNull('academic_year');
                })->count(),
        ];

        return view('attendance.index', [
            'attendances' => $attendances,
            'stats' => $stats,
            'date' => $date,
            'oldestAngkatan' => $angkatanItems->first()?->name ?? '-',
            'newestAngkatan' => $angkatanItems->last()?->name ?? '-',
        ]);
    }

    public function create()
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $students = Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*')
            ->orderBy('students.nama_lengkap')
            ->get();
        return view('attendance.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'status' => 'required|in:present,absent,late,excused',
            'location' => 'nullable',
        ]);

        // Auto-determine status if check_in is provided
        if ($validated['check_in']) {
            $checkInSetting = \App\Models\Setting::get('check_in_time', '08:00');
            $checkInLimit = date('H:i:s', strtotime($checkInSetting));
            $now = date('H:i:s', strtotime($validated['check_in']));
            $validated['status'] = $now > $checkInLimit ? 'late' : 'present';
        }

        $validated['academic_year'] = $this->getViewYear();

        Attendance::create($validated);

        // Check for rewards and penalties
        $student = Student::find($validated['student_id']);
        $this->checkAndRewardConsistentAttendance($student, $validated['date'], $validated['academic_year']);
        $this->checkAndApplyAttendancePenalties($student, $validated['date'], $validated['academic_year']);

        return redirect()->route('attendance')->with('success', 'Attendance record created.');
    }

    public function edit(Attendance $attendance)
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $students = Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*')
            ->orderBy('students.nama_lengkap')
            ->get();
        return view('attendance.edit', compact('attendance', 'students'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'status' => 'required|in:present,absent,late,excused',
            'location' => 'nullable',
        ]);

        // Auto-determine status if check_in is provided
        if ($validated['check_in']) {
            $checkInSetting = \App\Models\Setting::get('check_in_time', '08:00');
            $checkInLimit = date('H:i:s', strtotime($checkInSetting));
            $now = date('H:i:s', strtotime($validated['check_in']));
            $validated['status'] = $now > $checkInLimit ? 'late' : 'present';
        }

        $academicYear = $attendance->academic_year ?? $this->getViewYear();
        if (!$attendance->academic_year) {
            $validated['academic_year'] = $academicYear;
        }

        $attendance->update($validated);

        // Check for rewards and penalties
        $student = Student::find($validated['student_id']);
        $this->checkAndRewardConsistentAttendance($student, $validated['date'], $academicYear);
        $this->checkAndApplyAttendancePenalties($student, $validated['date'], $academicYear);

        return redirect()->route('attendance')->with('success', 'Attendance record updated.');
    }

    public function report(Request $request)
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));
        $angkatan = $request->input('angkatan');
        $search = $request->input('search');
        $mode = $request->input('mode', 'detail'); // 'detail' or 'summary'
        $angkatans = Angkatan::active()->ordered()->get();

        $angkatanCode = null;
        $angkatanId = null;
        if ($angkatan) {
            $angkatanObj = Angkatan::where('code', $angkatan)->first();
            if ($angkatanObj) {
                $angkatanId = $angkatanObj->id;
                $angkatanCode = $angkatanObj->code;
            }
        }

        $studentsQuery = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*');

        if ($angkatanId) {
            $studentsQuery->where('students.angkatan_id', $angkatanId);
        }
        if ($search) {
            $studentsQuery->where('students.nama_lengkap', 'like', '%' . $search . '%');
        }
        
        $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        $students = $studentsQuery->with(['attendances' => function($query) use ($start_date, $end_date, $viewYear, $currentYear) {
            $query->whereBetween('date', [$start_date, $end_date])
                  ->where(function($q) use ($viewYear, $currentYear) {
                      $q->where('academic_year', $viewYear);
                      if ($viewYear == $currentYear) {
                          $q->orWhereNull('academic_year');
                      }
                  });
        }])->get();
        
        $period = \Carbon\CarbonPeriod::create($start_date, $end_date);
        
        if ($mode === 'summary') {
            $months = [];
            foreach ($period as $date) {
                $monthKey = $date->format('Y-m');
                if (!isset($months[$monthKey])) {
                    $months[$monthKey] = [
                        'name' => $date->format('F Y'),
                        'key' => $monthKey
                    ];
                }
            }

            if ($request->has('print')) {
                $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
                $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');
                return view('attendance.print_summary_report', compact('students', 'months', 'start_date', 'end_date', 'headmaster', 'headmaster_nip', 'angkatan'));
            }

            return view('attendance.summary_report', compact('students', 'months', 'start_date', 'end_date', 'mode', 'angkatan', 'angkatans'));
        }

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        if ($request->has('print')) {
            $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
            $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');
            
            return view('attendance.print_report', compact('students', 'dates', 'start_date', 'end_date', 'headmaster', 'headmaster_nip', 'angkatan'));
        }
        
        return view('attendance.report', compact('students', 'dates', 'start_date', 'end_date', 'mode', 'angkatan', 'angkatans'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance')->with('success', 'Attendance record deleted.');
    }

    public function showBulkForm(Request $request)
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $angkatans = Angkatan::active()->ordered()->get();
        $date = $request->input('date', date('Y-m-d'));

        $angkatanSummary = [];
        foreach ($angkatans as $angkatan) {
            $studentsCount = \DB::table('academic_year_student')
                ->join('students', 'academic_year_student.student_id', '=', 'students.id')
                ->where('academic_year_student.academic_year_id', $viewYearId)
                ->where('students.angkatan_id', $angkatan->id)
                ->where('students.status', 0)
                ->count();

            $alreadyAbsent = Attendance::where('date', $date)
                ->where('academic_year', $viewYear)
                ->whereIn('student_id', function($query) use ($viewYearId, $angkatan) {
                    $query->select('students.id')
                        ->from('academic_year_student')
                        ->join('students', 'academic_year_student.student_id', '=', 'students.id')
                        ->where('academic_year_student.academic_year_id', $viewYearId)
                        ->where('students.angkatan_id', $angkatan->id);
                })
                ->count();

            $angkatanSummary[] = [
                'angkatan' => $angkatan,
                'total_students' => $studentsCount,
                'already_absent' => $alreadyAbsent,
                'pending' => max(0, $studentsCount - $alreadyAbsent),
            ];
        }

        return view('attendance.bulk', compact('angkatans', 'date', 'angkatanSummary', 'viewYear'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'angkatan_id' => 'required|exists:angkatans,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'check_in' => 'nullable',
            'location' => 'nullable',
        ]);

        $today = $validated['date'];
        $academicYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($academicYear);

        $isHoliday = \App\Models\Holiday::checkHoliday($today);
        if ($isHoliday) {
            $holidayName = \App\Models\Holiday::getHolidayName($today);
            return back()->with('error', 'Tanggal ' . \Carbon\Carbon::parse($today)->locale('id')->isoFormat('D MMMM Y') . ' adalah hari libur (' . $holidayName . '), tidak bisa melakukan absensi.')->withInput();
        }

        $checkInSetting = \App\Models\Setting::get('check_in_time', '08:00');
        $checkInLimit = date('H:i:s', strtotime($checkInSetting));

        $angkatan = Angkatan::findOrFail($validated['angkatan_id']);

        $students = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->where('students.angkatan_id', $validated['angkatan_id'])
            ->where('students.status', 0)
            ->select('students.*')
            ->orderBy('students.nama_lengkap')
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($students as $student) {
            $existing = Attendance::where('student_id', $student->id)
                ->where('date', $today)
                ->where(function($query) use ($academicYear) {
                    $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
                    $query->where('academic_year', $academicYear);
                    if ($academicYear == $currentYear) {
                        $query->orWhereNull('academic_year');
                    }
                })
                ->first();

            if ($existing) {
                $skippedCount++;
                continue;
            }

            $status = $validated['status'];
            $checkIn = $validated['check_in'] ?? null;

            if ($checkIn && in_array($status, ['present', 'late'])) {
                $now = date('H:i:s', strtotime($checkIn));
                $status = $now > $checkInLimit ? 'late' : 'present';
            }

            if (!$checkIn && in_array($status, ['present', 'late'])) {
                $checkIn = date('H:i:s');
                $status = $checkIn > $checkInLimit ? 'late' : 'present';
            }

            Attendance::create([
                'student_id' => $student->id,
                'date' => $today,
                'check_in' => in_array($status, ['present', 'late']) ? $checkIn : null,
                'status' => $status,
                'academic_year' => $academicYear,
                'location' => $validated['location'] ?? ('Bulk Absen - ' . $angkatan->name),
            ]);

            $this->checkAndRewardConsistentAttendance($student, $today, $academicYear);
            $this->checkAndApplyAttendancePenalties($student, $today, $academicYear);

            $createdCount++;
        }

        $message = 'Berhasil melakukan absen massal! ';
        $message .= $createdCount . ' siswa dicatat';
        if ($skippedCount > 0) {
            $message .= ', ' . $skippedCount . ' siswa dilewati (sudah absen)';
        }
        $message .= '.';

        return redirect()->route('attendance', ['date' => $today])->with('success', $message);
    }
}
