<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Validation\Rule;
use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    private function getViewYear()
    {
        return session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
    }

    public function index(Request $request)
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $currentYear = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
        
        $query = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*');

        // Filter by Status
        if ($request->has('status') && $request->status != 'all') {
            if ($request->status === 'active' || $request->status === '0') {
                $query->where('students.status', 0);
            } elseif ($request->status === 'inactive' || $request->status === '1') {
                $query->where('students.status', 1);
            }
        }

        if ($request->has('angkatan') && $request->angkatan != 'all') {
            $angkatan = Angkatan::where('code', $request->angkatan)->first();
            if ($angkatan) {
                $query->where('students.angkatan_id', $angkatan->id);
            }
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('students.nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('students.id_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('students.username', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->latest('students.created_at')->paginate(10)->withQueryString();
        $angkatans = Angkatan::active()->ordered()->get();

        $stats = [
            'total' => Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
                        ->where('academic_year_student.academic_year_id', $viewYearId)->count(),
            'active' => Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
                        ->where('academic_year_student.academic_year_id', $viewYearId)
                        ->where('students.status', 0)->count(),
            'pending' => Student::join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
                        ->where('academic_year_student.academic_year_id', $viewYearId)
                        ->where('students.status', 1)->count(),
            'on_leave' => 0,
        ];

        return view('students.index', [
            'students' => $students,
            'stats' => $stats,
            'angkatans' => $angkatans,
            'viewYear' => $viewYear,
            'currentYear' => $currentYear
        ]);
    }

    public function export()
    {
        $viewYear = $this->getViewYear();
        $cleanViewYear = str_replace(['/', '\\'], '-', $viewYear);
        $students = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->leftJoin('angkatans', 'students.angkatan_id', '=', 'angkatans.id')
            ->where('academic_year_student.academic_year_id', \App\Models\AcademicYear::getIdByName($viewYear))
            ->select(
                'students.id_siswa',
                'students.username',
                'students.pin',
                'students.status',
                'students.nama_lengkap',
                'angkatans.id as angkatan_id',
                'angkatans.code as angkatan_code',
                'angkatans.name as angkatan_name',
                'students.nisn',
                'students.no_nik',
                'students.gender',
                'students.mother_name',
                'students.religion',
                'students.height',
                'students.weight',
                'students.place_of_birth',
                'students.dateofbirth',
                'students.address',
                'students.nis_siswa',
                'students.email'
            )
            ->get();
        
        $filename = "students_export_" . $cleanViewYear . "_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID Siswa',
            'Username',
            'PIN',
            'Status',
            'Nama Lengkap',
            'ID Angkatan',
            'Kode Angkatan',
            'Nama Angkatan',
            'NISN',
            'No NIK',
            'Jenis Kelamin (L/P)',
            'Nama Ibu',
            'Agama',
            'Tinggi Badan',
            'Berat Badan',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Alamat',
            'NIS Siswa',
            'Email'
        ];

        $callback = function() use($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id_siswa,
                    $student->username,
                    $student->pin,
                    $student->status,
                    $student->nama_lengkap,
                    $student->angkatan_id ?? '-',
                    $student->angkatan_code ?? '-',
                    $student->angkatan_name ?? '-',
                    $student->nisn ?? '-',
                    $student->no_nik ?? '-',
                    $student->gender ?? '-',
                    $student->mother_name ?? '-',
                    $student->religion ?? '-',
                    $student->height ?? '-',
                    $student->weight ?? '-',
                    $student->place_of_birth ?? '-',
                    $student->dateofbirth ?? '-',
                    $student->address ?? '-',
                    $student->nis_siswa ?? '-',
                    $student->email ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel()
    {
        $viewYear = $this->getViewYear();
        $cleanViewYear = str_replace(['/', '\\'], '-', $viewYear);
        $filename = "students_export_" . $cleanViewYear . "_" . date('Ymd_His') . ".xlsx";
        return Excel::download(new StudentsExport($viewYear), $filename);
    }

    public function exportPdf(Request $request)
    {
        $viewYear = $this->getViewYear();
        $viewYearId = \App\Models\AcademicYear::getIdByName($viewYear);
        $cleanViewYear = str_replace(['/', '\\'], '-', $viewYear);
        
        $query = Student::query()
            ->join('academic_year_student', 'students.id', '=', 'academic_year_student.student_id')
            ->leftJoin('angkatans', 'students.angkatan_id', '=', 'angkatans.id')
            ->where('academic_year_student.academic_year_id', $viewYearId)
            ->select('students.*', 'angkatans.code as angkatan_code', 'angkatans.name as angkatan_name');

        if ($request->has('status') && $request->status != 'all') {
            if ($request->status === 'active' || $request->status === '0') {
                $query->where('students.status', 0);
            } elseif ($request->status === 'inactive' || $request->status === '1') {
                $query->where('students.status', 1);
            }
        }

        if ($request->has('angkatan') && $request->angkatan != 'all') {
            $angkatan = Angkatan::where('code', $request->angkatan)->first();
            if ($angkatan) {
                $query->where('students.angkatan_id', $angkatan->id);
            }
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('students.nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('students.id_siswa', 'like', '%' . $search . '%')
                  ->orWhere('students.username', 'like', '%' . $search . '%')
                  ->orWhere('students.nisn', 'like', '%' . $search . '%')
                  ->orWhere('students.email', 'like', '%' . $search . '%');
            });
        }

        $students = $query->latest('students.created_at')->get();

        $stats = [
            'total' => $students->count(),
            'active' => $students->where('status', 0)->count(),
            'inactive' => $students->where('status', 1)->count(),
        ];

        $logoDataUri = null;
        $logoPath = public_path('images/axioo.png');
        if (is_file($logoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('students.report_pdf', [
            'students' => $students,
            'stats' => $stats,
            'viewYear' => $viewYear,
            'search' => $request->search ?? '',
            'statusFilter' => $request->status ?? 'all',
            'angkatanFilter' => $request->angkatan ?? 'all',
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
            'appName' => config('app.name', 'Smart Axioo'),
        ])->setPaper('a4', 'landscape');

        $filename = "laporan_data_siswa_" . $cleanViewYear . "_" . date('Ymd_His') . ".pdf";

        return $pdf->download($filename);
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport(), 'template_siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        $file = $request->file('file');
        
        $import = new StudentsImport;
        Excel::import($import, $file);
        
        $imported = $import->imported;
        $errors = $import->errors;
        $errorMessages = $import->errorMessages ?? [];

        if ($errors > 0 || count($errorMessages) > 0) {
            return redirect()->back()
                ->with('import_errors', $errorMessages)
                ->with('imported', $imported)
                ->with('error_count', $errors);
        }

        return redirect()->back()->with('success', "Import selesai! Berhasil diimport: $imported data.");
    }

    public function printCards(Request $request)
    {
        $studentIds = $request->input('ids');
        
        if ($studentIds) {
            $students = Student::whereIn('id', explode(',', $studentIds))->get();
        } else {
            $students = Student::all();
        }

        return view('students.cards', compact('students'));
    }

    public function create()
    {
        $angkatans = Angkatan::active()->ordered()->get();

        return view('students.create', compact('angkatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|unique:students',
            'username' => 'required|unique:students',
            'pin' => 'required|digits:6',
            'status' => 'required|in:0,1,active,inactive,on leave',
            'nama_lengkap' => 'required',
            'angkatan_id' => ['required', Rule::exists('angkatans', 'id')->where('is_active', true)],
            'foto' => 'nullable',
            'nisn' => 'nullable',
            'no_nik' => 'nullable',
            'gender' => 'nullable|in:0,1,L,P',
            'mother_name' => 'nullable',
            'religion' => 'nullable',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'place_of_birth' => 'nullable',
            'dateofbirth' => 'nullable|date',
            'address' => 'nullable',
            'nis_siswa' => 'nullable',
            'email' => 'required|email|unique:students',
            'phone' => 'nullable|string',
            'parent_phone' => 'nullable|string',
        ]);

        // Map gender and status values
        if (isset($validated['gender'])) {
            if (in_array($validated['gender'], ['L', '0'])) {
                $validated['gender'] = 0;
            } elseif (in_array($validated['gender'], ['P', '1'])) {
                $validated['gender'] = 1;
            }
        }

        if (isset($validated['status'])) {
            if (in_array($validated['status'], ['active', '0'])) {
                $validated['status'] = 0;
            } elseif (in_array($validated['status'], ['inactive', 'on leave', '1'])) {
                $validated['status'] = 1;
            }
        }

        $angkatan = Angkatan::find($validated['angkatan_id']);
        $validated['angkatan'] = $angkatan->code;

        $academicYear = \App\Models\Setting::get('academic_year');
        $academicYearId = \App\Models\AcademicYear::getIdByName($academicYear);

        $student = Student::create($validated);

        // Create enrollment record (use updateOrCreate to avoid duplicates)
        \App\Models\AcademicYearStudent::updateOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $academicYearId],
            []
        );

        return redirect()->route('students')->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['attendances', 'points']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $angkatans = Angkatan::active()->ordered()->get();

        return view('students.edit', compact('student', 'angkatans'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|unique:students,id_siswa,' . $student->id,
            'username' => 'required|unique:students,username,' . $student->id,
            'pin' => 'nullable|digits:6',
            'status' => 'required|in:0,1,active,inactive,on leave',
            'nama_lengkap' => 'required',
            'angkatan_id' => ['required', Rule::exists('angkatans', 'id')->where('is_active', true)],
            'foto' => 'nullable',
            'nisn' => 'nullable',
            'no_nik' => 'nullable',
            'gender' => 'nullable|in:0,1,L,P',
            'mother_name' => 'nullable',
            'religion' => 'nullable',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'place_of_birth' => 'nullable',
            'dateofbirth' => 'nullable|date',
            'address' => 'nullable',
            'nis_siswa' => 'nullable',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string',
            'parent_phone' => 'nullable|string',
        ]);

        if (!$request->filled('pin')) {
            unset($validated['pin']);
        }

        // Map gender and status values
        if (isset($validated['gender'])) {
            if (in_array($validated['gender'], ['L', '0'])) {
                $validated['gender'] = 0;
            } elseif (in_array($validated['gender'], ['P', '1'])) {
                $validated['gender'] = 1;
            }
        }

        if (isset($validated['status'])) {
            if (in_array($validated['status'], ['active', '0'])) {
                $validated['status'] = 0;
            } elseif (in_array($validated['status'], ['inactive', 'on leave', '1'])) {
                $validated['status'] = 1;
            }
        }

        $angkatan = Angkatan::find($validated['angkatan_id']);
        $validated['angkatan'] = $angkatan->code;

        $student->update($validated);

        // Update enrollment record for the current academic year
        $currentYear = \App\Models\Setting::get('academic_year');
        $currentYearId = \App\Models\AcademicYear::getIdByName($currentYear);
        
        if ($currentYearId) {
            \App\Models\AcademicYearStudent::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $currentYearId],
                []
            );
        }

        return redirect()->route('students')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students')->with('success', 'Student deleted successfully.');
    }

    public function resetPin(Student $student)
    {
        // Reset PIN ke default: 123456
        $student->update(['pin' => '123456']);
        return redirect()->route('students')->with('success', 'PIN siswa berhasil direset ke 123456.');
    }
}
