<?php

namespace App\Http\Controllers;

use App\Models\StudentPermit;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPermitController extends Controller
{
    private function getViewYear()
    {
        return session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
    }

    public function index()
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $permits = StudentPermit::with(['student'])
            ->where('academic_year', $viewYear)
            ->latest()
            ->get();

        $students = Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.permits.index', compact('permits', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:sakit,izin,dispen',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($request->hasFile('proof_photo')) {
            $validated['proof_photo'] = $request->file('proof_photo')->store('permits', 'public');
        }

        $validated['academic_year'] = $this->getViewYear();

        StudentPermit::create($validated);

        return back()->with('success', 'Izin siswa berhasil ditambahkan oleh admin.');
    }

    public function update(Request $request, StudentPermit $permit)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:sakit,izin,dispen',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'proof_photo' => 'nullable|image|max:2048',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($request->hasFile('proof_photo')) {
            if ($permit->proof_photo) {
                Storage::disk('public')->delete($permit->proof_photo);
            }
            $validated['proof_photo'] = $request->file('proof_photo')->store('permits', 'public');
        }

        if (!$permit->academic_year) {
            $validated['academic_year'] = $this->getViewYear();
        }

        $permit->update($validated);

        return back()->with('success', 'Data izin berhasil diperbarui.');
    }

    public function updateStatus(Request $request, StudentPermit $permit)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $permit->update(['status' => $validated['status']]);

        // If approved, create/update attendance record
        if ($validated['status'] === 'approved') {
            $academicYear = $permit->academic_year ?? \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
            
            // Loop through date range if end_date exists
            $start = \Carbon\Carbon::parse($permit->start_date);
            $end = $permit->end_date ? \Carbon\Carbon::parse($permit->end_date) : $start;
            
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                \App\Models\Attendance::updateOrCreate(
                    [
                        'student_id' => $permit->student_id,
                        'date' => $date->format('Y-m-d'),
                        'academic_year' => $academicYear,
                    ],
                    [
                        'status' => 'excused',
                        'location' => 'Permit: ' . ucfirst($permit->type),
                        'check_in' => null,
                    ]
                );
            }
        }
        
        return back()->with('success', 'Status pengajuan izin diperbarui.');
    }

    public function destroy(StudentPermit $permit)
    {
        if ($permit->proof_photo) {
            Storage::disk('public')->delete($permit->proof_photo);
        }
        $permit->delete();
        return back()->with('success', 'Data izin berhasil dihapus.');
    }
}
