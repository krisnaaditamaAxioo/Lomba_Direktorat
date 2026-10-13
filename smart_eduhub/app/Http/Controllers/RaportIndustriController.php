<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\RaportIndustri;
use App\Models\RaportCapaianPembelajaran;
use App\Models\RaportUjiSertifikasi;
use App\Models\Student;
use App\Exports\RaportTemplateExport;
use App\Imports\RaportImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaportIndustriController extends Controller
{
    /** Default aspek capaian pembelajaran */
    private const DEFAULT_ASPEK = [
        1 => 'Sikap dan kemauan kerja',
        2 => 'Kreatif dan inovatif kerja',
        3 => 'Ketrampilan menggunakan Alat',
        4 => 'Kedisiplinan dan tanggung jawab',
        5 => 'Kemauan Melaksanakan tugas',
    ];

    /** Default ujian sertifikasi */
    private const DEFAULT_UJIAN = [
        1 => 'UJI SERTIFIKASI ACP 1',
        2 => 'UJI SERTIFIKASI ACP 2',
        3 => 'UJI SERTIFIKASI ACP 3',
    ];

    public function index(Request $request)
    {
        $angkatans = Angkatan::ordered()->get();

        $query = RaportIndustri::with(['student', 'angkatan.waliKelas']);

        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }
        if ($request->filled('tahun_pelajaran')) {
            $query->where('tahun_pelajaran', $request->tahun_pelajaran);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('id_siswa', 'like', "%{$search}%")
                  ->orWhere('nis_siswa', 'like', "%{$search}%");
            });
        }

        // Wali Kelas hanya bisa lihat angkatan yang dia ampu
        if (auth()->user()->isWaliKelas() && !auth()->user()->isAdmin()) {
            $myAngkatan = Angkatan::where('wali_kelas_id', auth()->id())->pluck('id');
            $query->whereIn('angkatan_id', $myAngkatan);
        }

        $rapports = $query->latest()->paginate(20)->withQueryString();

        // Unique tahun pelajaran untuk filter
        $tahunList = RaportIndustri::distinct()->orderBy('tahun_pelajaran')->pluck('tahun_pelajaran');

        return view('raport.index', compact('rapports', 'angkatans', 'tahunList'));
    }

    public function create(Request $request)
    {
        $angkatans = Angkatan::with('waliKelas')->ordered()->get();

        // Ambil siswa berdasarkan angkatan terpilih
        $selectedAngkatanId = $request->angkatan_id;
        $students = $selectedAngkatanId
            ? Student::where('angkatan_id', $selectedAngkatanId)->where('status', 0)->orderBy('nama_lengkap')->get()
            : collect();

        $defaultAspek  = self::DEFAULT_ASPEK;
        $defaultUjian  = self::DEFAULT_UJIAN;

        // Suggested tahun pelajaran
        $tahunPelajaran = session('current_academic_year', date('Y') . '/' . (date('Y') + 1) . '_Ganjil');

        return view('raport.create', compact('angkatans', 'students', 'defaultAspek', 'defaultUjian', 'tahunPelajaran', 'selectedAngkatanId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'         => 'required|exists:students,id',
            'angkatan_id'        => 'required|exists:angkatans,id',
            'tahun_pelajaran'    => 'required|string|max:50',
            'tingkat'            => 'nullable|string|max:50',
            'program_keahlian'   => 'nullable|string|max:100',
            'trainer_name'       => 'nullable|string|max:100',
            'trainer_no_serti'   => 'nullable|string|max:50',
            'trainer_nbm'        => 'nullable|string|max:50',
            'catatan'            => 'nullable|string',
            'aspek'              => 'required|array|min:1',
            'aspek.*.aspek_penilaian' => 'required|string',
            'aspek.*.predikat'   => 'required|string|in:A,B,C,D',
            'aspek.*.keterangan' => 'nullable|string',
            'ujian'              => 'nullable|array',
            'ujian.*.nama_ujian' => 'required_with:ujian|string',
            'ujian.*.no_serti'   => 'nullable|string',
            'ujian.*.status'     => 'required_with:ujian|string|in:LULUS,TIDAK LULUS',
        ], [
            'student_id.required' => 'Siswa wajib dipilih.',
            'tahun_pelajaran.required' => 'Tahun pelajaran wajib diisi.',
            'aspek.*.predikat.in' => 'Predikat harus A, B, C, atau D.',
        ]);

        DB::beginTransaction();
        try {
            $raport = RaportIndustri::create([
                'student_id'       => $validated['student_id'],
                'angkatan_id'      => $validated['angkatan_id'],
                'tahun_pelajaran'  => $validated['tahun_pelajaran'],
                'tingkat'          => $validated['tingkat'] ?? null,
                'program_keahlian' => $validated['program_keahlian'] ?? null,
                'trainer_name'     => $validated['trainer_name'] ?? null,
                'trainer_no_serti' => $validated['trainer_no_serti'] ?? null,
                'trainer_nbm'      => $validated['trainer_nbm'] ?? null,
                'catatan'          => $validated['catatan'] ?? null,
            ]);

            // Capaian Pembelajaran
            foreach ($validated['aspek'] as $i => $item) {
                RaportCapaianPembelajaran::create([
                    'raport_id'       => $raport->id,
                    'no'              => $i + 1,
                    'aspek_penilaian' => $item['aspek_penilaian'],
                    'predikat'        => $item['predikat'],
                    'keterangan'      => $item['keterangan'] ?? null,
                ]);
            }

            // Uji Sertifikasi
            if (!empty($validated['ujian'])) {
                foreach ($validated['ujian'] as $j => $ujian) {
                    if (!empty($ujian['nama_ujian'])) {
                        RaportUjiSertifikasi::create([
                            'raport_id'  => $raport->id,
                            'no'         => $j + 1,
                            'nama_ujian' => $ujian['nama_ujian'],
                            'no_serti'   => $ujian['no_serti'] ?? null,
                            'status'     => $ujian['status'] ?? 'LULUS',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('raport.index')->with('success', 'Raport berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(RaportIndustri $raport)
    {
        $raport->load(['student', 'angkatan.waliKelas', 'capaianPembelajaran', 'ujiSertifikasi']);
        $angkatans = Angkatan::with('waliKelas')->ordered()->get();
        $students  = Student::where('angkatan_id', $raport->angkatan_id)->where('status', 0)->orderBy('nama_lengkap')->get();
        $defaultAspek = self::DEFAULT_ASPEK;
        $defaultUjian = self::DEFAULT_UJIAN;

        return view('raport.edit', compact('raport', 'angkatans', 'students', 'defaultAspek', 'defaultUjian'));
    }

    public function update(Request $request, RaportIndustri $raport)
    {
        $validated = $request->validate([
            'tahun_pelajaran'    => 'required|string|max:50',
            'tingkat'            => 'nullable|string|max:50',
            'program_keahlian'   => 'nullable|string|max:100',
            'trainer_name'       => 'nullable|string|max:100',
            'trainer_no_serti'   => 'nullable|string|max:50',
            'trainer_nbm'        => 'nullable|string|max:50',
            'catatan'            => 'nullable|string',
            'aspek'              => 'required|array|min:1',
            'aspek.*.aspek_penilaian' => 'required|string',
            'aspek.*.predikat'   => 'required|string|in:A,B,C,D',
            'aspek.*.keterangan' => 'nullable|string',
            'ujian'              => 'nullable|array',
            'ujian.*.nama_ujian' => 'required_with:ujian|string',
            'ujian.*.no_serti'   => 'nullable|string',
            'ujian.*.status'     => 'required_with:ujian|string|in:LULUS,TIDAK LULUS',
        ]);

        DB::beginTransaction();
        try {
            $raport->update([
                'tahun_pelajaran'  => $validated['tahun_pelajaran'],
                'tingkat'          => $validated['tingkat'] ?? null,
                'program_keahlian' => $validated['program_keahlian'] ?? null,
                'trainer_name'     => $validated['trainer_name'] ?? null,
                'trainer_no_serti' => $validated['trainer_no_serti'] ?? null,
                'trainer_nbm'      => $validated['trainer_nbm'] ?? null,
                'catatan'          => $validated['catatan'] ?? null,
            ]);

            // Re-create capaian pembelajaran
            $raport->capaianPembelajaran()->delete();
            foreach ($validated['aspek'] as $i => $item) {
                RaportCapaianPembelajaran::create([
                    'raport_id'       => $raport->id,
                    'no'              => $i + 1,
                    'aspek_penilaian' => $item['aspek_penilaian'],
                    'predikat'        => $item['predikat'],
                    'keterangan'      => $item['keterangan'] ?? null,
                ]);
            }

            // Re-create uji sertifikasi
            $raport->ujiSertifikasi()->delete();
            if (!empty($validated['ujian'])) {
                foreach ($validated['ujian'] as $j => $ujian) {
                    if (!empty($ujian['nama_ujian'])) {
                        RaportUjiSertifikasi::create([
                            'raport_id'  => $raport->id,
                            'no'         => $j + 1,
                            'nama_ujian' => $ujian['nama_ujian'],
                            'no_serti'   => $ujian['no_serti'] ?? null,
                            'status'     => $ujian['status'] ?? 'LULUS',
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('raport.index')->with('success', 'Raport berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(RaportIndustri $raport)
    {
        $raport->delete();
        return back()->with('success', 'Raport berhasil dihapus.');
    }

    public function print(RaportIndustri $raport)
    {
        $raport->load(['student.points', 'angkatan.waliKelas', 'capaianPembelajaran', 'ujiSertifikasi']);

        $allPoints = $raport->student?->points ?? collect();
        $violations = $allPoints->where('type', 'violation')->sortByDesc('date');
        $achievements = $allPoints->where('type', 'achievement')->sortByDesc('date');
        $totalViolationScore = $violations->sum('score');
        $totalAchievementScore = $achievements->sum('score');

        return view('raport.print', compact('raport', 'violations', 'achievements', 'totalViolationScore', 'totalAchievementScore'));
    }

    /**
     * AJAX: get students by angkatan_id
     */
    public function getStudents(Request $request)
    {
        $angkatanId = $request->angkatan_id;
        $students = Student::where('angkatan_id', $angkatanId)
            ->where('status', 0)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'id_siswa', 'nis_siswa', 'nisn']);

        return response()->json($students);
    }

    public function downloadTemplate()
    {
        return Excel::download(new RaportTemplateExport(), 'template_raport_industri.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt'
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.'
        ]);

        try {
            $import = new RaportImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->imported;
            $errors = $import->errors;
            $errorMessages = $import->errorMessages;

            if ($errors > 0) {
                return back()
                    ->with('imported', $imported)
                    ->with('import_errors', $errorMessages)
                    ->with('warning', "Import selesai dengan catatan. Berhasil: {$imported} data, Gagal: {$errors} data.");
            }

            return back()->with('success', "Import berhasil! {$imported} data raport telah diproses.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file import: ' . $e->getMessage());
        }
    }

    public function printBulk(Request $request)
    {
        $request->validate([
            'angkatan_id' => 'required|exists:angkatans,id',
        ], [
            'angkatan_id.required' => 'Pilih Angkatan terlebih dahulu untuk cetak massal.',
        ]);

        $query = RaportIndustri::with(['student.points', 'angkatan.waliKelas', 'capaianPembelajaran', 'ujiSertifikasi'])
            ->where('angkatan_id', $request->angkatan_id);

        if ($request->filled('tahun_pelajaran')) {
            $query->where('tahun_pelajaran', $request->tahun_pelajaran);
        }

        if (auth()->user()->isWaliKelas() && !auth()->user()->isAdmin()) {
            $myAngkatan = Angkatan::where('wali_kelas_id', auth()->id())->pluck('id')->toArray();
            if (!in_array($request->angkatan_id, $myAngkatan)) {
                abort(403, 'Anda tidak memiliki akses cetak untuk angkatan ini.');
            }
        }

        $rapports = $query->latest()->get();

        if ($rapports->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan data raport untuk angkatan terpilih.');
        }

        $selectedAngkatan = Angkatan::with('waliKelas')->find($request->angkatan_id);

        return view('raport.print_bulk', compact('rapports', 'selectedAngkatan'));
    }
}
