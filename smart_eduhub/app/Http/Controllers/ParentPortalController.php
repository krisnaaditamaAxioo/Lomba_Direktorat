<?php

namespace App\Http\Controllers;

use App\Models\StudentPermit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    private function getParentStudent()
    {
        $parent = Auth::guard('parent')->user();
        return $parent->student;
    }

    public function dashboard()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;

        if (!$student) {
            return view('parent.dashboard', [
                'parent' => $parent,
                'student' => null,
                'recentAttendances' => collect(),
                'recentPoints' => collect(),
                'netPoints' => 0,
                'activePermit' => null,
                'violationAlert' => null,
                'attendanceRate' => 0,
                'attendanceStatus' => 'Tidak Ada Data',
                'statusColor' => 'slate',
            ]);
        }

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        // Presensi 5 Terakhir
        $recentAttendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->latest('date')
            ->take(5)
            ->get();

        // Point kedisiplinan
        $recentPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->latest('date')
            ->take(5)
            ->get();

        $totalViolationPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->where('type', 'violation')
            ->sum('score');

        $totalRewardPoints = $student->points()
            ->where('academic_year', $academicYear)
            ->where('type', 'achievement')
            ->sum('score');

        $initialPoints = 100;
        $netPoints = $initialPoints - $totalViolationPoints + $totalRewardPoints;

        // Permohonan izin aktif
        $activePermit = $student->permits()
            ->where('academic_year', $academicYear)
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Peringatan Poin
        $violationAlert = null;
        if ($netPoints <= 50) {
            $violationAlert = [
                'level' => 'danger',
                'title' => 'Peringatan Kedisiplinan Kritis!',
                'message' => "Sisa poin kedisiplinan anak Anda saat ini tinggal {$netPoints} poin. Harap melakukan pembinaan dan bimbingan.",
            ];
        } elseif ($netPoints <= 75) {
            $violationAlert = [
                'level' => 'warning',
                'title' => 'Peringatan Kedisiplinan (Poin Rendah)',
                'message' => "Sisa poin kedisiplinan anak Anda adalah {$netPoints} poin.",
            ];
        }

        // Statistik Presensi
        $totalDays = $student->attendances()->where('academic_year', $academicYear)->count();
        $presentDays = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['present', 'late'])
            ->count();

        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 100;

        if ($attendanceRate >= 90) {
            $attendanceStatus = 'Sangat Baik';
            $statusColor = 'emerald';
        } elseif ($attendanceRate >= 75) {
            $attendanceStatus = 'Cukup Baik';
            $statusColor = 'amber';
        } else {
            $attendanceStatus = 'Perlu Perhatian';
            $statusColor = 'rose';
        }

        return view('parent.dashboard', compact(
            'parent',
            'student',
            'recentAttendances',
            'recentPoints',
            'netPoints',
            'activePermit',
            'violationAlert',
            'attendanceRate',
            'attendanceStatus',
            'statusColor'
        ));
    }

    public function attendance(Request $request)
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));

        $attendances = $student->attendances()
            ->where('academic_year', $academicYear)
            ->whereBetween('date', [$start_date, $end_date])
            ->latest()
            ->get();

        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'excused' => 0,
        ];

        foreach ($attendances as $att) {
            if ($att->status === 'present') $stats['present']++;
            elseif ($att->status === 'late') $stats['late']++;
            elseif ($att->status === 'excused') $stats['excused']++;
            elseif ($att->status === 'absent') $stats['absent']++;
        }

        $period = \Carbon\CarbonPeriod::create($start_date, $end_date);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        if ($request->has('print')) {
            $headmaster = \App\Models\Setting::get('headmaster_name', '..........................');
            $headmaster_nip = \App\Models\Setting::get('headmaster_nip', '..........................');

            return view('parent.print_attendance', compact(
                'parent',
                'student',
                'attendances',
                'dates',
                'start_date',
                'end_date',
                'headmaster',
                'headmaster_nip'
            ));
        }

        return view('parent.attendance', compact('parent', 'student', 'attendances', 'stats', 'dates', 'start_date', 'end_date'));
    }

    public function finance()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $cards = $student->cards;
        $requests = $student->cardRequests()->latest()->get();

        $cardIds = $cards->pluck('id')->toArray();
        $transactions = \App\Models\StudentCardTransaction::whereIn('card_id', $cardIds)
            ->latest()
            ->paginate(15);

        return view('parent.finance', compact('parent', 'student', 'cards', 'requests', 'transactions'));
    }

    public function points()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $points = $student->points()
            ->where('academic_year', $academicYear)
            ->latest()
            ->paginate(10);

        return view('parent.points', compact('parent', 'student', 'points'));
    }

    public function permit()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $permits = $student->permits()->latest()->get();

        return view('parent.permit', compact('parent', 'student', 'permits'));
    }

    public function storePermit(Request $request)
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $validated = $request->validate([
            'type' => 'required|in:sakit,izin,dispen',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
        ]);

        $validated['student_id'] = $student->id;
        $validated['status'] = 'pending';
        $validated['academic_year'] = $academicYear;

        if ($request->hasFile('proof_photo')) {
            $path = $request->file('proof_photo')->store('permits', 'public');
            $validated['proof_photo'] = $path;
        }

        StudentPermit::create($validated);

        return back()->with('success', 'Pengajuan izin sakit/keperluan oleh Wali Murid berhasil dikirim.');
    }

    public function letters()
    {
        $parent = Auth::guard('parent')->user();
        $student = $parent->student;
        if (!$student) return back()->with('error', 'Siswa tidak ditemukan.');

        $academicYear = session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));

        $letters = \App\Models\Letter::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->latest()
            ->get();

        return view('parent.letters', compact('parent', 'student', 'letters'));
    }

    public function notifications()
    {
        $parent = Auth::guard('parent')->user();
        $notifications = $parent->notifications()->latest()->paginate(20);

        return view('parent.notifications', compact('parent', 'notifications'));
    }

    public function markNotificationRead(\App\Models\ParentNotification $notification)
    {
        $parent = Auth::guard('parent')->user();
        abort_unless($notification->parent_id === $parent->id, 403);

        $notification->update(['read' => true]);

        return redirect()->route('parent.letters');
    }

    public function complaints()
    {
        $parent = Auth::guard('parent')->user();
        $complaints = $parent->complaints()->latest()->get();

        return view('parent.complaints', compact('parent', 'complaints'));
    }

    public function storeComplaint(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('proof_photo')) {
            $path = $request->file('proof_photo')->store('complaints', 'public');
            $validated['proof_photo'] = $path;
        }

        $parent->complaints()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'proof_photo' => $validated['proof_photo'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengaduan berhasil dikirim ke Pihak Sekolah/Admin.');
    }
}
