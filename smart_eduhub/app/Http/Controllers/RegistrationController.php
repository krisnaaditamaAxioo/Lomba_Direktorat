<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Registration;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    private function resolveAutoAngkatan(): ?Angkatan
    {
        $currentYear = (int) now()->format('Y');

        $angkatan = Angkatan::active()
            ->where('tahun_masuk', $currentYear)
            ->ordered()
            ->first();

        if ($angkatan) {
            return $angkatan;
        }

        $fallback = Angkatan::active()
            ->orderByRaw('tahun_masuk IS NULL')
            ->orderBy('tahun_masuk', 'desc')
            ->orderBy('sort_order', 'desc')
            ->first();

        return $fallback;
    }

    public function showForm()
    {
        $autoAngkatan = $this->resolveAutoAngkatan();
        $angkatans = Angkatan::all();
        $whatsappGroupLink = \App\Models\Setting::get('whatsapp_group_link', '');
        
        if ($angkatans->isEmpty()) {
            return view('registrations.form', compact('autoAngkatan', 'angkatans', 'whatsappGroupLink'))->withErrors(['angkatan' => 'Pendaftaran belum dibuka karena master angkatan belum tersedia. Silakan hubungi admin.']);
        }

        return view('registrations.form', compact('autoAngkatan', 'angkatans', 'whatsappGroupLink'));
    }

    private function getViewYear()
    {
        return session('view_academic_year', \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1)));
    }

    public function submitForm(Request $request)
    {
        $angkatans = Angkatan::all();
        
        if ($angkatans->isEmpty()) {
            return back()->withErrors(['angkatan' => 'Pendaftaran belum dibuka. Silakan hubungi admin.']);
        }
        
        $autoAngkatan = $this->resolveAutoAngkatan();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'no_nik' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'mother_name' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:50',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'place_of_birth' => 'nullable|string|max:255',
            'dateofbirth' => 'nullable|date',
            'nis_siswa' => 'nullable|string|max:20',
            'previous_school' => 'required|string|max:255',
            'address' => 'required|string',
        ];
        
        if (!$autoAngkatan) {
            $rules['angkatan'] = 'required|exists:angkatans,code';
        }
        
        $validated = $request->validate($rules);

        if ($autoAngkatan) {
            $validated['angkatan'] = $autoAngkatan->code;
        }
        $validated['application_id'] = 'REG-' . strtoupper(uniqid());
        $validated['status'] = 'pending';
        $validated['academic_year'] = \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        Registration::create($validated);

        return redirect()->route('registrations.track', ['id' => $validated['application_id']])
            ->with('success', 'Pendaftaran berhasil dikirim! Simpan Nomor pendaftaran Anda: ' . $validated['application_id']);
    }

    public function trackStatus(Request $request)
    {
        $id = $request->query('id');
        $nisn = $request->query('nisn');
        $registration = null;
        $whatsappGroupLink = \App\Models\Setting::get('whatsapp_group_link', '');

        if ($id) {
            $registration = Registration::where('application_id', $id)->first();
        } elseif ($nisn) {
            $registration = Registration::where('nisn', $nisn)->first();
            $id = $nisn;
        }

        return view('registrations.proses', compact('registration', 'id', 'whatsappGroupLink'));
    }

    public function downloadInfo(Request $request)
    {
        $id = $request->query('id');
        $nisn = $request->query('nisn');

        $registration = null;

        if ($id) {
            $registration = Registration::where('application_id', $id)->first();
        } elseif ($nisn) {
            $registration = Registration::where('nisn', $nisn)->first();
            $id = $nisn;
        }

        if (! $registration) {
            abort(404);
        }

        $statusLabel = match ($registration->status) {
            'pending' => 'Sedang Antri',
            'verified' => 'Sedang Diproses',
            'approved' => 'Diterima',
            'rejected' => 'Ditolak',
            default => ucfirst((string) $registration->status),
        };

        $logoDataUri = null;
        $logoPath = public_path('images/axioo.png');
        if (is_file($logoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $whatsappGroupLink = \App\Models\Setting::get('whatsapp_group_link', '');
        $whatsappQrCode = null;
        if ($whatsappGroupLink) {
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($whatsappGroupLink);
            $whatsappQrCode = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        }

        $view = in_array($registration->status, ['approved', 'rejected'], true)
            ? 'registrations.selection_result_pdf'
            : 'registrations.download_info_pdf';

        $pdf = Pdf::loadView($view, [
            'registration' => $registration,
            'statusLabel' => $statusLabel,
            'trackUrl' => route('registrations.track', ['id' => $registration->application_id]),
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
            'appName' => config('app.name', 'Smart Axioo'),
            'whatsappGroupLink' => $whatsappGroupLink,
            'whatsappQrCode' => $whatsappQrCode,
        ])->setPaper('a4', 'portrait');

        $filename = 'pendaftaran_' . $registration->application_id . '.pdf';

        return $pdf->download($filename);
    }

    public function showCheckStatus()
    {
        return view('registrations.cek_status');
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $viewYear = $this->getViewYear();
        $search = $request->query('search', '');
        
        $query = Registration::where(function($q) use ($viewYear) {
            $q->where('academic_year', $viewYear)
              ->orWhereNull('academic_year');
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'processed') {
            $registrations = $query->whereIn('status', ['verified', 'rejected', 'approved'])->latest()->get();
        } else {
            $registrations = $query->where('status', 'pending')->latest()->get();
        }

        $stats = [
            'pending' => Registration::where('status', 'pending')
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
            'processed' => Registration::whereIn('status', ['verified', 'rejected', 'approved'])
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
        ];

        return view('registrations.index', compact('registrations', 'stats', 'status', 'viewYear', 'search'));
    }

    public function printReport(Request $request)
    {
        $status = $request->query('status', 'pending');
        $viewYear = $this->getViewYear();
        $search = $request->query('search', '');
        
        $query = Registration::where(function($q) use ($viewYear) {
            $q->where('academic_year', $viewYear)
              ->orWhereNull('academic_year');
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'processed') {
            $registrations = $query->whereIn('status', ['verified', 'rejected', 'approved'])->latest()->get();
        } else {
            $registrations = $query->where('status', 'pending')->latest()->get();
        }

        $stats = [
            'pending' => Registration::where('status', 'pending')
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
            'verified' => Registration::where('status', 'verified')
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
            'approved' => Registration::where('status', 'approved')
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
            'rejected' => Registration::where('status', 'rejected')
                ->where(function($q) use ($viewYear) {
                    $q->where('academic_year', $viewYear)->orWhereNull('academic_year');
                })->count(),
        ];

        $statusLabel = $status === 'processed' ? 'Sudah Diproses' : 'Pending';
        
        $logoDataUri = null;
        $logoPath = public_path('images/axioo.png');
        if (is_file($logoPath)) {
            $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('registrations.report_pdf', [
            'registrations' => $registrations,
            'stats' => $stats,
            'statusLabel' => $statusLabel,
            'status' => $status,
            'viewYear' => $viewYear,
            'search' => $search,
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
            'appName' => config('app.name', 'Smart Axioo'),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_pendaftaran_' . $status . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function show(Registration $registration)
    {
        return response()->json($registration);
    }

    public function updateStatus(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,incomplete,rejected,approved',
        ]);

        DB::transaction(function () use ($registration, $validated) {
            $registration->update($validated);

            if ($validated['status'] === 'approved') {
                // Check if student already exists
                $existingStudent = Student::where('email', $registration->email)->first();
                $academicYear = $registration->academic_year ?? \App\Models\Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));
                $academicYearId = \App\Models\AcademicYear::getIdByName($academicYear);
                
                if (!$existingStudent) {
                    // Mapping gender dari string L/P ke integer
                    $gender = strtoupper(trim((string)$registration->gender));
                    if ($gender === 'L' || $gender === '0') {
                        $genderValue = 0;
                    } elseif ($gender === 'P' || $gender === '1') {
                        $genderValue = 1;
                    } else {
                        $genderValue = (int)$gender;
                    }

                    // Cari angkatan_id dari kode angkatan
                    $angkatanId = null;
                    if (!empty($registration->angkatan)) {
                        $angkatan = \App\Models\Angkatan::where('code', $registration->angkatan)->first();
                        if ($angkatan) {
                            $angkatanId = $angkatan->id;
                        }
                    }

                    $student = Student::create([
                        'id_siswa' => 'STU-' . strtoupper(uniqid()),
                        'nama_lengkap' => $registration->name,
                        'email' => $registration->email,
                        'address' => $registration->address,
                        'previous_school' => $registration->previous_school,
                        'nisn' => $registration->nisn,
                        'no_nik' => $registration->no_nik,
                        'gender' => $genderValue,
                        'mother_name' => $registration->mother_name,
                        'religion' => $registration->religion,
                        'height' => $registration->height ? (int)$registration->height : null,
                        'weight' => $registration->weight ? (int)$registration->weight : null,
                        'place_of_birth' => $registration->place_of_birth,
                        'dateofbirth' => $registration->dateofbirth,
                        'nis_siswa' => $registration->nis_siswa,
                        'angkatan' => $registration->angkatan,
                        'angkatan_id' => $angkatanId,
                        'status' => 0,
                        'role' => 'student',
                        'username' => strtolower(explode(' ', $registration->name)[0]) . rand(100, 999),
                        'pin' => '123456',
                    ]);

                    // Create enrollment record
                    \App\Models\AcademicYearStudent::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYearId,
                    ]);
                    
                    return; 
                }
            }
        });

        // Check if registration exists
        $message = "Registration status updated.";
        if ($request->status === 'approved') {
            $message = "Registration approved and student record created successfully.";
        }

        return redirect()->route('registrations')->with('success', $message);
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('registrations')->with('success', 'Pendaftaran berhasil dihapus.');
    }
}
