# 08 — Penjelasan Kode Penting Smart EduHub

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

Dokumen ini berisi penjelasan bagian-bagian kode penting dalam aplikasi Smart EduHub. Setiap anggota tim menjelaskan minimal dua bagian kode yang berbeda sesuai bidang tugasnya masing-masing (lihat `02-rencana.md`). Bagian yang dijelaskan tidak tumpang tindih antar anggota.

---

## Ringkasan Pembagian Tugas

| Anggota | Bidang | Bagian Kode yang Dijelaskan |
|---------|--------|-------------------------------|
| **Krisna Aditama** (Ketua) | Arsitektur sistem, autentikasi, keuangan, integrasi | `AuthController::loginForRole` + dashboardRouteFor`, `AdminFinanceController::topupCard` |
| **Najih Banyu Wicaksono** | Pendaftaran, absensi, ujian, poin | `RegistrationController::submitForm + resolveAutoAngkatan`, `AttendanceController::processScan` |
| **Mujahid Muhammad Abul Ghofur** | E-learning, izin, portal siswa/orang tua | `CourseController::storeGradeSubmission`, `StudentPermitController::updateStatus` |

---

## 1. Autentikasi Multi-Guard (Overview)

**File:** `config/auth.php`, `app/Models/User.php`

Sistem menggunakan 3 guard session terpisah:

- `web` → admin, guru, teller, BUMS (model `User`)
- `student` → siswa (model `Student`)
- `parent` → orang tua/wali (model `ParentUser`)

Model `User` memiliki 7 role: `admin`, `guru`, `wali_kelas`, `wakasis`, `bk`, `teller`, `bums`. Role `teller` dan `bums` memiliki izin keuangan setara admin melalui method `isFinanceStaff()`.

---

# Penjelasan Kode per Anggota

---

## 2. Krisna Aditama — Autentikasi & Keuangan

### 2.1 Fungsi Login Berbasis Role

**Penulis:** Krisna Aditama  
**File:** `app/Http/Controllers/AuthController.php`  
**Fungsi:** `loginForRole(Request $request, string $role)` dan `dashboardRouteFor(User $user)`

**Kode:**

```php
private function loginForRole(Request $request, string $role)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $user = Auth::user();
        $roleMatches = $role === 'guru'
            ? $user->isGuru()
            : in_array($user->role, ['admin', 'wali_kelas', 'wakasis', 'bk', 'teller', 'bums'], true);

        if (!$roleMatches) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun ini tidak dapat digunakan untuk login ' . ($role === 'guru' ? 'guru.' : 'admin.'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->route($this->dashboardRouteFor($user));
    }

    return back()->withErrors([
        'email' => 'Email atau password tidak cocok untuk login ' . ($role === 'guru' ? 'guru.' : 'admin.'),
    ])->onlyInput('email');
}

private function dashboardRouteFor(User $user): string
{
    if ($user->isGuru())   return 'guru.dashboard';
    if ($user->isTeller()) return 'admin.finance.dashboard';
    if ($user->isBums())   return 'admin.finance.payments.history';
    return 'dashboard';
}
```

**Apa yang dilakukan:**
- `loginForRole` memvalidasi email & password, mencoba login via `Auth::attempt`, lalu memastikan role user cocok dengan halaman login yang dipakai (guru hanya bisa lewat halaman guru; admin/wali_kelas/wakasis/bk/teller/bums lewat halaman admin). Jika cocok, session di-regenerate lalu user diarahkan ke dashboard sesuai rolenya.
- `dashboardRouteFor` menentukan rute tujuan setelah login: guru → `guru.dashboard`, teller → `admin.finance.dashboard`, bums → `admin.finance.payments.history`, selain itu → `dashboard`.

**Kenapa dibuat seperti itu:**
- Satu tabel `users` menampung semua staf (admin, guru, teller, bums, dll.), jadi cukup satu form login. Pemisahan berdasarkan `role` dilakukan di level logika, bukan guard.
- `dashboardRouteFor` membedakan landing page agar teller langsung masuk ke operasional keuangan dan BUMS ke rekap pembayaran, tanpa perlu navigasi manual.
- `session()->regenerate()` mencegah session fixation.

**Contoh nilai masuk dan hasilnya:**

| Input | Hasil |
|-------|-------|
| `email=admin@smartaxioo.com`, `password=password`, `role=admin` | Login berhasil, redirect ke `dashboard` |
| `email=teller@sekolah.sch.id`, `password=secret`, `role=admin` | Login berhasil, redirect ke `admin.finance.dashboard` |
| `email=bums@sekolah.sch.id`, `password=secret`, `role=admin` | Login berhasil, redirect ke `admin.finance.payments.history` |
| `email=guru@sekolah.sch.id`, `password=secret`, `role=guru` | Login berhasil, redirect ke `guru.dashboard` |

**Apa yang terjadi jika input salah/kosong:**
- Email kosong / bukan format email → validasi gagal, kembali ke form dengan pesan error `The email field is required`.
- Password kosong → validasi gagal, pesan `The password field is required`.
- Email benar tapi password salah → kembali ke form dengan pesan `Email atau password tidak cocok untuk login admin.` (field email tetap terisi.
- Email/password benar tapi role tidak cocok (mis. akun guru login di halaman admin) → `Auth::logout()` dipanggil, kembali dengan pesan `Akun ini tidak dapat digunakan untuk login admin.`

---

### 2.2 Top-Up Saldo Kartu Siswa

**Penulis:** Krisna Aditama  
**File:** `app/Http/Controllers/AdminFinanceController.php`  
**Fungsi:** `topupCard(Request $request, StudentCard $card)`

**Kode:****

```php
public function topupCard(Request $request, StudentCard $card)
{
    $this->authorizeFinance('canTopupFinance');
    $validated = $request->validate([
        'amount'      => 'required|numeric|min:1000',
        'description' => 'nullable|string|max:255'
    ]);

    DB::beginTransaction();
    try {
        $balanceBefore = $card->balance;
        $card->balance += $validated['amount'];
        $card->save();

        StudentCardTransaction::create([
            'card_id'        => $card->id,
            'type'           => 'topup',
            'amount'         => $validated['amount'],
            'balance_before' => $balanceBefore,
            'balance_after'  => $card->balance,
            'description'    => $validated['description'] ?? 'Topup oleh admin',
            'performed_by'   => auth()->id()
        ]);

        DB::commit();
        return back()->with('success', 'Topup berhasil');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

**Apa yang dilakukan:**
- Mengecek izin (`canTopupFinance` → admin/teller/bums), memvalidasi nominal minimal Rp1.000, membungkus update saldo + pencatatan transaksi dalam satu transaksi database. Saldo kartu ditambah nominal, lalu dibuat record `StudentCardTransaction` dengan `balance_before` dan `balance_after`. Jika sukses di-commit, jika gagal di-rollback.

**Kenapa dibuat seperti itu:**
- `DB::beginTransaction` + `commit`/`rollBack` memastikan saldo kartu dan riwayat transaksi selalu konsisten—tidak mungkin saldo berubah tanpa ada catatan transaksi (atau sebaliknya).
- `authorizeFinance` memastikan hanya admin, teller, dan bums yang bisa top-up.
- `min:1000` mencegah top-up nominal nol/negatif.
- `balance_before` & `balance_after` disimpan untuk kebutuhan audit.

**Contoh nilai masuk dan hasilnya:**

| Input (kartu saldo Rp50.000) | Hasil |
|-------------------------------|-------|
| `amount=20000`, `description=Top up jajan` | Saldo jadi Rp70.000, transaksi `topup` tercatat |
| `amount=1000`, `description=kosong` | Saldo jadi Rp51.000, transaksi tercatat dengan deskripsi `Topup oleh admin` |

**Apa yang terjadi jika input salah/kosong:**
- `amount` kosong → validasi gagal, pesan `The amount field is required`.
- `amount=500` (di bawah 1000) → validasi gagal, pesan `The amount must be at least 1000`.
- `amount` bukan angka (mis. `abc`) → validasi gagal, pesan `The amount must be a number`.
- `description` lebih dari 255 karakter → validasi gagal.
- Jika terjadi error di tengah proses (mis. DB error) → `rollBack`, saldo kembali semula, pesan error ditampilkan.

---

## 3. Najih Banyu Wicaksono — Pendaftaran & Absensi

### 3.1 Submit Form Pendaftaran Siswa Baru

**Penulis:** Najih Banyu Wicaksono  
**File:** `app/Http/Controllers/RegistrationController.php`  
**Fungsi:** `resolveAutoAngkatan()` dan `submitForm(Request $request)`

**Kode:****

```php
private function resolveAutoAngkatan(): ?Angkatan
{
    $currentYear = (int) now()->format('Y');

    $angkatan = Angkatan::active()
        ->where('tahun_masuk', $currentYear)
        ->ordered()
        ->first();

    if ($angkatan) return $angkatan;

    return Angkatan::active()
        ->orderByRaw('tahun_masuk IS NULL')
        ->orderBy('tahun_masuk', 'desc')
        ->orderBy('sort_order', 'desc')
        ->first();
}

public function submitForm(Request $request)
{
    $angkatans = Angkatan::all();
    if ($angkatans->isEmpty()) {
        return back()->withErrors(['angkatan' => 'Pendaftaran belum dibuka. Silakan hubungi admin.']);
    }

    $autoAngkatan = $this->resolveAutoAngkatan();

    $rules = [
        'name'             => 'required|string|max:255',
        'email'            => 'required|email|max:255',
        'phone'            => 'required|string|max:20',
        'previous_school'  => 'required|string|max:255',
        'address'          => 'required|string',
        'nisn'             => 'nullable|string|max:20',
        'gender'           => 'nullable|in:L,P',
        // ... field lain nullable
    ];

    if (!$autoAngkatan) {
        $rules['angkatan'] = 'required|exists:angkatans,code';
    }

    $validated = $request->validate($rules);

    if ($autoAngkatan) {
        $validated['angkatan'] = $autoAngkatan->code;
    }

    $validated['application_id'] = 'REG-' . strtoupper(uniqid());
    $validated['status']         = 'pending';
    $validated['academic_year']  = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

    Registration::create($validated);

    return redirect()->route('registrations.track', ['id' => $validated['application_id']])
        ->with('success', 'Pendaftaran berhasil dikirim! Simpan Nomor pendaftaran Anda: ' . $validated['application_id']);
}
```

**Apa yang dilakukan:**
- `resolveAutoAngkatan` mencoba mencari angkatan aktif yang `tahun_masuk`-nya sama dengan tahun berjalan. Jika tidak ada, fallback ke angkatan aktif terbaru (urut `tahun_masuk` desc, lalu `sort_order` desc).
- `submitForm` mengecek master angkatan tersedia, menyiapkan aturan validasi, jika tidak ada angkatan otomatis maka field `angkatan` wajib diisi user. Lalu membuat `application_id` unik (`REG-XXXX`), status `pending`, dan tahun ajaran dari setting. Data disimpan lalu user diarahkan ke halaman cek status.

**Kenapa dibuat seperti itu:**
- Auto-angkatan memudahkan calon siswa tidak perlu memilih angkatan secara manual—tahun berjalan otomatis dipakai. Fallback menjaga agar form tetap bisa dipakai meski data angkatan belum lengkap.
- `application_id` unik dipakai sebagai nomor pendaftaran yang mudah dilacak calon siswa.
- `status=pending` default agar admin perlu verifikasi sebelum status diubah (verified/accepted/rejected).
- Tahun ajaran diambil dari setting global agar konsisten.

**Contoh nilai masuk dan hasilnya:**

| Input | Hasil |
|-------|-------|
| `name=Budi`, `email=budi@mail.com`, `phone=08123`, `previous_school=SMP 1`, `address=Jl. Mawar` (tahun ini ada angkatan 2026) | Data tersimpan, `application_id=REG-6543...`, `angkatan=2026`, `status=pending`, redirect ke `/cek-status?id=REG-...` |
| Sama tapi tidak ada angkatan tahun berjalan, user pilih `angkatan=2025` | `angkatan=2025`, data tersimpan |

**Apa yang terjadi jika input salah/kosong:**
- Master angkatan kosong → kembali dengan pesan `Pendaftaran belum dibuka. Silakan hubungi admin.`
- `name`, `email`, `phone`, `previous_school`, atau `address` kosong → validasi gagal dengan pesan field required.
- `email` bukan format email → `The email must be a valid email address`.
- `gender` diisi selain `L`/`P` → validasi gagal.
- Tidak ada angkatan otomatis dan field `angkatan` tidak diisi → `The angkatan field is required`.

---

### 3.2 Scan Absensi QR Code

**Penulis:** Najih Banyu Wicaksono  
**File:** `app/Http/Controllers/AttendanceController.php`  
**Fungsi:** `processScan(Request $request)`

**Kode:****

```php
public function processScan(Request $request)
{
    $scanCode = trim($request->input('id_siswa'));

    $student = Student::where('id_siswa', $scanCode)
        ->orWhere('nis_siswa', $scanCode)
        ->orWhere('nisn', $scanCode)
        ->first();

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan dengan ID: ' . $scanCode], 404);
    }

    if ($student->status !== 'active') {
        return response()->json(['success' => false, 'message' => 'Status siswa ' . $student->nama_lengkap . ' tidak aktif.'], 403);
    }

    $today = date('Y-m-d');
    $now   = date('H:i:s');
    $currentYear = Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

    if (Holiday::checkHoliday($today)) {
        return response()->json(['success' => false, 'message' => 'Hari ini adalah hari libur...'], 403);
    }

    $checkInSetting = Setting::get('check_in_time', '08:00');
    $checkInLimit   = date('H:i:s', strtotime($checkInSetting));

    $existing = Attendance::where('student_id', $student->id)
        ->where('date', $today)
        ->where('academic_year', $currentYear)
        ->first();

    if ($existing) {
        return response()->json(['success' => false, 'message' => $student->nama_lengkap . ' sudah melakukan absensi hari ini.'], 400);
    }

    $status = $now > $checkInLimit ? 'late' : 'present';

    Attendance::create([
        'student_id'    => $student->id,
        'date'          => $today,
        'check_in'      => $now,
        'status'        => $status,
        'academic_year' => $currentYear,
        'location'      => 'Main Gate (Scanner)'
    ]);

    $this->checkAndRewardConsistentAttendance($student, $today, $currentYear);
    $this->checkAndApplyAttendancePenalties($student, $today, $currentYear);

    return response()->json([
        'success' => true,
        'message' => 'Absensi berhasil! Selamat belajar, ' . $student->nama_lengkap . '.',
        'student' => $student->nama_lengkap,
        'time'    => date('H:i A', strtotime($now)),
        'status'  => $status
    ]);
}
```

**Apa yang dilakukan:**
- Menerima `id_siswa` dari hasil scan QR, mencari siswa berdasarkan `id_siswa`, `nis_siswa`, atau `nisn`. Memeriksa status siswa aktif, hari ini bukan hari libur, dan siswa belum absen hari ini. Menentukan status `present` atau `late` berdasarkan `check_in_time` dari setting. Membuat record absensi, lalu menjalankan logika reward/penalty konsistensi, lalu mengembalikan JSON sukses.

**Kenapa dibuat seperti itu:**
- Pencarian via 3 kolom (`id_siswa`, `nis_siswa`, `nisn`) membuat QR fleksibel—kartu bisa berisi salah satu.
- Pengecekan hari libur & duplikasi absen mencegah data ganda.
- `check_in_time` dinamis dari setting agar admin bisa atur batas keterlambatan tanpa ubah kode.
- Reward & penalty otomatis mengaitkan absensi dengan sistem poin.

**Contoh nilai masuk dan hasilnya:**

| Input (hari kerja jam 07:30, batas 08:00 | Hasil |
|----------------------------------------|-------|
| `id_siswa=12345` (siswa aktif, belum absen) | `{success:true, status:present, message:"Absensi berhasil! Selamat belajar, Budi."} |
| `id_siswa=12345` jam 08:15 | `status:late` |
| `id_siswa=12345` scan ulang hari yang sama | `{success:false, message:"Budi sudah melakukan absensi hari ini."} (HTTP 400) |

**Apa yang terjadi jika input salah/kosong:**
- `id_siswa` kosong → `$scanCode` kosong, query tidak cocok → `{success:false, message:"Siswa tidak ditemukan dengan ID: }` HTTP 404.
- ID tidak terdaftar → HTTP 404 "Siswa tidak ditemukan".
- Siswa non-aktif → HTTP 403 "Status siswa ... tidak aktif".
- Hari libur → HTTP 403 "Hari ini adalah hari libur".

---

## 4. Mujahid Muhammad Abul Ghofur — E-Learning & Izin

### 4.1 Penilaian Tugas Modul

**Penulis:** Mujahid Muhammad Abul Ghofur  
**File:** `app/Http/Controllers/CourseController.php`  
**Fungsi:** `storeGradeSubmission(Request $request, AssignmentSubmission $submission)`

**Kode:****

```php
public function storeGradeSubmission(Request $request, AssignmentSubmission $submission)
{
    $request->validate([
        'question_scores'   => 'nullable|array',
        'question_scores.*' => 'nullable|integer|min:0|max:100',
        'feedback'          => 'nullable|string|max:5000',
    ]);

    $submission->load('module.questions');
    $questionScores = $request->input('question_scores', []);
    $totalScore = 0;
    $maxScore = $submission->module->questions->sum('score') ?: 100;

    foreach ($submission->module->questions as $q) {
        $scoreGiven = isset($questionScores[$q->id]) ? (int) $questionScores[$q->id] : 0;
        $scoreGiven = min($scoreGiven, $q->score);
        $questionScores[$q->id] = $scoreGiven;
        $totalScore += $scoreGiven;
    }

    $submission->update([
        'score'          => $totalScore,
        'max_score'      => $maxScore,
        'question_scores' => $questionScores,
        'status'         => 'graded',
        'feedback'       => $request->input('feedback'),
    ]);

    ModuleProgress::updateOrCreate(
        ['student_id' => $submission->student_id, 'module_id' => $submission->module_id],
        ['progress' => 100, 'completed_at' => now(), 'last_seen_at' => now()]
    );

    return back()->with('success', 'Penilaian & Feedback untuk siswa berhasil disimpan!');
}
```

**Apa yang dilakukan:**
- Menerima nilai per soal (`question_scores`) dan feedback. Menjumlahkan nilai tiap soal (dibatasi `min(bobot soal)`), menghitung `max_score` dari total bobot soal. Menyimpan total nilai, status jadi `graded`, feedback. Lalu membuat/men-update `ModuleProgress` jadi 100% (selesai).

**Kenapa dibuat seperti itu:**
- Perhitungan otomatis dari nilai per soal mengurangi kesalahan hitung manual guru.
- `min($scoreGiven, $q->score)` memastikan nilai tidak melebihi bobot soal.
- `ModuleProgress` di-set 100% saat dinilai agar modul dianggap selesai—progress siswa otomatis ter-update.
- `max_score` disimpan untuk menghindari perubahan bobot soal di kemudian hari.

**Contoh nilai masuk dan hasilnya:**

Misal modul punya 2 soal: soal A (bobot 60), soal B (bobot 40)

| Input | Hasil |
|-------|-------|
| `question_scores[A]=50, [B]=30, feedback="Bagus" | `score=80`, `max_score=100`, status `graded`, progress 100% |
| `question_scores[A]=70` (lebih dari bobot 60), [B]=40 | `score=100` (A dipotong jadi 60), `max_score=100` |
| `question_scores` kosong, feedback kosong | `score=0`, `max_score=100`, feedback null |

**Apa yang terjadi jika input salah/kosong:**
- `question_scores.*` diisi bukan integer → validasi gagal `The question_scores.0 must be an integer`.
- Nilai soal < 0 atau > 100 → validasi gagal (tapi di-loop juga dibatasi `min($score`).
- `feedback` > 5000 karakter → validasi gagal.
- `question_scores` tidak dikirim → default `[]`, total 0, status `graded`.

---

### 4.2 Persetujuan Izin Siswa

**Penulis:** Mujahid Muhammad Abul Ghofur  
**File:** `app/Http/Controllers/StudentPermitController.php`  
**Fungsi:** `updateStatus(Request $request, StudentPermit $permit)`

**Kode:****

```php
public function updateStatus(Request $request, StudentPermit $permit)
{
    $validated = $request->validate([
        'status' => 'required|in:approved,rejected',
    ]);

    $permit->update(['status' => $validated['status']]);

    if ($validated['status'] === 'approved') {
        $academicYear = $permit->academic_year ?? Setting::get('academic_year', date('Y') . '-' . (date('Y') + 1));

        $start = Carbon::parse($permit->start_date);
        $end   = $permit->end_date ? Carbon::parse($permit->end_date) : $start;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            Attendance::updateOrCreate(
                [
                    'student_id'    => $permit->student_id,
                    'date'          => $date->format('Y-m-d'),
                    'academic_year' => $academicYear,
                ],
                [
                    'status'   => 'excused',
                    'location' => 'Permit: ' . ucfirst($permit->type),
                    'check_in' => null,
                ]
            );
        }
    }

    return back()->with('success', 'Status pengajuan izin diperbarui.');
}
```

**Apa yang dilakukan:**
- Memvalidasi status hanya `approved` atau `rejected`. Mengupdate status izin. Jika `approved`, loop dari `start_date` sampai `end_date` (atau hanya `start_date` jika `end_date` null), membuat atau meng-update record `Attendance` dengan status `excused` dan lokasi `Permit: <tipe_izin`.

**Kenapa dibuat seperti itu:**
- Saat izin disetujui, absensi otomatis jadi `excused` agar siswa tidak tercatat alpa. Loop tanggal mulai sampai tanggal selesai menangani izin multi-hari.
- `updateOrCreate` mencegah duplikasi jika absen di tanggal yang sama.
- `location` diisi tipe izin agar jelas sumber excused berasal dari izin.

**Contoh nilai masuk dan hasilnya:**

Misal izin `type=sakit, start=2026-10-10, end=2026-10-12

| Input | Hasil |
|-------|-------|
| `status=approved` | 3 record Attendance (10, 11, 12 Okt) status `excused`, location `Permit: Sakit` |
| `status=approved`, `end_date=null | 1 record Attendance tanggal start_date |
| `status=rejected` | status izin `rejected`, tidak ada perubahan absensi |

**Apa yang terjadi jika input salah/kosong:**
- `status` kosong → validasi gagal `The status field is required`.
- `status` diisi selain `approved`/`rejected` → validasi gagal `The selected status is invalid`.
- `start_date` null (data lama) → `Carbon::parse(null)` jadi hari ini, loop hanya 1 hari (hari ini).
