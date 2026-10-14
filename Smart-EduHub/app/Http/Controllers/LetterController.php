<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Point;
use App\Models\Setting;
use App\Models\Letter;
use App\Models\ParentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterController extends Controller
{
    public function index()
    {
        $academicYear = session('view_academic_year', Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        
        $query = Letter::with('student')
            ->where('academic_year', $academicYear);

        $letters = $query->latest()->paginate(10);

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        $students = Student::orderBy('nama_lengkap')->get();
        return view('letters.create', compact('students'));
    }

    public function store(Request $request)
    {
        $academicYear = session('view_academic_year', Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:binaan,sp1,sp2,sp3,lainnya',
            'status' => 'required|in:draft,sent',
            'points' => 'nullable|integer|min:0',
            'result_note' => 'nullable|string|max:5000',
            'reference_number' => 'nullable|string|max:255',
            'issued_at' => 'required|date',
        ]);

        $validated['academic_year'] = $academicYear;
        $validated['points'] = $validated['points'] ?? 0;
        $letter = Letter::create($validated);

        $student = Student::findOrFail($validated['student_id']);
        $typeLabel = match ($letter->type) {
            'binaan' => 'Surat Binaan',
            'sp1' => 'Surat Peringatan 1',
            'sp2' => 'Surat Peringatan 2',
            'sp3' => 'Surat Peringatan 3',
            default => 'Surat Resmi',
        };

        if ($letter->status === 'sent') {
            ParentUser::where('student_id', $student->id)->get()->each(function (ParentUser $parent) use ($letter, $student, $typeLabel) {
                $parent->notifications()->create([
                    'type' => 'letter',
                    'title' => $typeLabel . ' Baru',
                    'message' => 'Surat resmi baru untuk ' . $student->nama_lengkap . ' telah diterbitkan. Silakan buka menu Surat Resmi untuk melihat detailnya.',
                    'data' => ['letter_id' => $letter->id],
                ]);
            });
        }

        return redirect()->route('letters.index')->with('success', 'Surat berhasil dibuat dan notifikasi dikirim ke wali murid.');
    }

    public function updateStatus(Request $request, Letter $letter)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,received,processed',
            'result_note' => 'nullable|string'
        ]);

        $letter->update($validated);

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function destroy(Letter $letter)
    {
        $letter->delete();
        return redirect()->route('letters.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function print(Student $student, $type)
    {
        // Security check for student portal
        if (Auth::guard('student')->check()) {
            $loggedInStudentId = Auth::guard('student')->id();
            if ($loggedInStudentId != $student->id) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        } else {
            // For admin portal, ensure admin is logged in
            if (!Auth::check()) {
                return redirect()->route('login');
            }
        }

        $academicYear = session('view_academic_year', Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
        
        $totalViolation = Point::where('student_id', $student->id)
            ->where('type', 'violation')
            ->where('academic_year', $academicYear)
            ->sum('score');

        $totalAchievement = Point::where('student_id', $student->id)
            ->where('type', 'achievement')
            ->where('academic_year', $academicYear)
            ->sum('score');

        $netPoints = max(0, $totalViolation - $totalAchievement);

        $headmaster = Setting::get('headmaster_name', '..........................');
        $headmaster_nip = Setting::get('headmaster_nip', '..........................');
        $school_name = Setting::get('school_name', 'Smart Axioo Class Program');

        return view('letters.print', compact('student', 'type', 'netPoints', 'headmaster', 'headmaster_nip', 'school_name', 'academicYear'));
    }
}
