# Bug Hunting Report — Smart-ACP

**Tanggal Analisis:** Oktober 2026  
**Analis:** Kiro AI  
**Framework:** Laravel 12 / PHP 8.2  
**Metode:** Static code analysis + route tracing + logic review

---

## Ringkasan Temuan

| ID | Severitas | Kategori | Judul |
|----|-----------|----------|-------|
| BUG-01 | 🔴 High | Keamanan | Route `/clear-cache` & `/show-logs` tidak diproteksi |
| BUG-02 | 🔴 High | Keamanan | Quick Login tanpa password tidak ada rate-limit |
| BUG-03 | 🟠 Medium | Keamanan | PIN siswa disimpan dan dibandingkan sebagai plain text |
| BUG-04 | 🟠 Medium | Logika | Class `AttendanceReportHelper` tidak ada meski test-nya ada |
| BUG-05 | 🟡 Low | Logika | Duplikat route nama `registrations.form` & `registrations.submit` |
| BUG-06 | 🟡 Low | Logika | `processScan` cek `$student->status !== 'active'` tapi status disimpan sebagai integer |
| BUG-07 | 🟡 Low | Logika | `stopImpersonateGuru` memeriksa `quick_login_from_login_page` yang bisa hapus sesi admin |
| BUG-08 | 🟡 Low | Kualitas | `distribution` di `PointController::index()` bisa bagi dengan nol meski ada guard |
| BUG-09 | 🟢 Info | Kualitas | `checkAndRewardConsistentAttendance` adalah placeholder kosong |
| BUG-10 | 🟢 Info | Keamanan | Endpoint ujian masuk bisa diakses tanpa autentikasi |

---

## BUG-01 — Route Utilitas Tidak Diproteksi

### Severitas
🔴 **High** — Eksposur informasi server di production

### Lokasi
```
routes/web.php, baris ~30–45
```

### Kode Bermasalah
```php
// Siapa saja bisa akses tanpa login!
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return 'Cache cleared!';
});

Route::get('/show-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    // ...
    $lastLines = array_slice($lines, -50);
    return '<pre>' . implode('', $lastLines) . '</pre>';
});
```

### Dampak
- `/clear-cache` bisa di-trigger siapapun → cache dihapus, route/config di-clear → downtime
- `/show-logs` menampilkan 50 baris terakhir log Laravel kepada publik → eksposur stack trace, path file, data request

### Rekomendasi Perbaikan
```php
// Pindahkan ke dalam middleware auth dan tambahkan cek role admin
Route::middleware(['auth'])->group(function () {
    Route::get('/clear-cache', function () {
        abort_unless(auth()->user()->isAdmin(), 403);
        // ... artisan calls
    });

    Route::get('/show-logs', function () {
        abort_unless(auth()->user()->isAdmin(), 403);
        // ... log display
    });
});
```

---

## BUG-02 — Quick Login Tanpa Password Tidak Memiliki Proteksi Tambahan

### Severitas
🔴 **High** — Bypass autentikasi jika mekanisme ini bisa dipanggil bebas

### Lokasi
```
routes/web.php — Route::post('/quick-login/*')
app/Http/Controllers/AuthController.php — quickLogin(), quickLoginStudent(), quickLoginParent()
```

### Kode Bermasalah
```php
// Route publik, siapa saja bisa POST
Route::post('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login.user');
Route::post('/quick-login/siswa/{student}', [AuthController::class, 'quickLoginStudent'])->name('quick-login.student');

// Controller hanya cek role, langsung login tanpa password
public function quickLoginStudent(Request $request, Student $student)
{
    $request->session()->put('quick_login_from_login_page', true);
    Auth::guard('student')->login($student, true); // ← login tanpa verifikasi apapun
    $request->session()->regenerate();
    return redirect()->intended(route('siswa.dashboard'));
}
```

### Dampak
- Siapapun yang tahu `id` User/Student bisa login sebagai mereka dengan POST request sederhana
- Tidak ada rate-limiting, token one-time, atau verifikasi perangkat di sisi server
- Jika dijangkau dari jaringan publik, semua akun bisa diambil alih

### Konteks
Fitur ini dirancang untuk login cepat dari halaman login menggunakan data `localStorage` browser. Namun proteksi ini hanya di sisi client, tidak di server.

### Rekomendasi Perbaikan
```php
// Opsi 1: Hapus route ini di production (hanya aktif di development)
if (app()->isLocal()) {
    Route::post('/quick-login/{user}', ...);
}

// Opsi 2: Tambahkan signed URL + expiry
Route::post('/quick-login/{user}', [AuthController::class, 'quickLogin'])
    ->middleware('signed')
    ->name('quick-login.user');

// Opsi 3: Tambahkan rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/quick-login/{user}', ...);
});
```

---

## BUG-03 — PIN Siswa Disimpan dan Dibandingkan sebagai Plain Text

### Severitas
🟠 **Medium** — Data breach PIN jika database bocor

### Lokasi
```
app/Http/Controllers/StudentFinanceController.php — processTransfer()
app/Models/Student.php — kolom 'pin'
app/Models/StudentCard.php — kolom 'pin' (hidden tapi plain text)
```

### Kode Bermasalah
```php
// StudentFinanceController::processTransfer()
if ($student->pin !== $request->pin) {
    return back()->with('error', 'PIN yang Anda masukkan salah.');
}
// PIN dibandingkan langsung sebagai plain text string
```

```php
// Student model — pin ada di fillable tapi tanpa cast 'hashed'
protected $fillable = [
    // ...
    'pin',
    // ...
];
// Tidak ada: protected $casts = ['pin' => 'hashed'];
```

### Dampak
- Jika database bocor, semua PIN siswa terbaca langsung
- PIN digunakan untuk transaksi keuangan (transfer e-wallet) — risiko finansial
- Berbeda dengan `password` User yang menggunakan `Hash::make()`

### Rekomendasi Perbaikan
```php
// 1. Tambahkan cast 'hashed' di Student model
protected function casts(): array
{
    return [
        'pin' => 'hashed',
    ];
}

// 2. Ubah perbandingan PIN menggunakan Hash::check()
use Illuminate\Support\Facades\Hash;

if (!Hash::check($request->pin, $student->pin)) {
    return back()->with('error', 'PIN yang Anda masukkan salah.');
}

// 3. Lakukan migrasi data PIN yang sudah ada
// php artisan make:command HashExistingPins
```

---

## BUG-04 — Class `AttendanceReportHelper` Tidak Ditemukan

### Severitas
🟠 **Medium** — Test akan langsung error (class not found), helper tidak bisa digunakan

### Lokasi
```
tests/Unit/AttendanceReportHelperTest.php — use App\Support\AttendanceReportHelper;
```

### Penjelasan
Test unit yang sudah ada di `tests/Unit/AttendanceReportHelperTest.php` mengimport class `App\Support\AttendanceReportHelper`, namun class tersebut **tidak pernah dibuat**. Direktori `app/Support/` tidak ada.

### Dampak
```
Error: Class "App\Support\AttendanceReportHelper" not found
```
Test suite gagal saat dijalankan. Helper yang dimaksud (merangkum data absensi) juga tidak bisa digunakan di view laporan.

### Rekomendasi Perbaikan
Buat file `app/Support/AttendanceReportHelper.php`:

```php
<?php

namespace App\Support;

class AttendanceReportHelper
{
    /**
     * Rangkum koleksi absensi dalam rentang tanggal.
     *
     * @param  iterable  $attendances  Objek atau array dengan key 'status' dan 'date'
     * @return array{present: int, late: int, hadir: int, absent: int, excused: int, sick: int}
     */
    public static function summarizePeriod($attendances, string $startDate, string $endDate): array
    {
        $summary = [
            'present' => 0,
            'late'    => 0,
            'hadir'   => 0,
            'absent'  => 0,
            'excused' => 0,
            'sick'    => 0,
        ];

        foreach ($attendances as $record) {
            $status = is_array($record) ? ($record['status'] ?? null) : ($record->status ?? null);
            $date   = is_array($record) ? ($record['date'] ?? null) : ($record->date ?? null);

            if ($date !== null && ($date < $startDate || $date > $endDate)) {
                continue;
            }

            match ($status) {
                'present' => [$summary['present']++, $summary['hadir']++],
                'late'    => [$summary['late']++, $summary['hadir']++],
                'absent'  => $summary['absent']++,
                'excused' => $summary['excused']++,
                'sick'    => $summary['sick']++,
                default   => null,
            };
        }

        return $summary;
    }
}
```

---

## BUG-05 — Duplikat Nama Route untuk Pendaftaran

### Severitas
🟡 **Low** — Route terakhir yang terdaftar menang, tapi bisa menyebabkan kebingungan dan link yang salah

### Lokasi
```
routes/web.php — ~baris 55–60 dan ~baris 380–385
```

### Kode Bermasalah
```php
// Definisi pertama (baris ~55)
Route::get('/daftar', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/daftar', [RegistrationController::class, 'submitForm'])->name('registrations.submit');

// Definisi kedua (baris ~380) — nama route sama!
Route::get('/join', [RegistrationController::class, 'showForm'])->name('registrations.form');
Route::post('/join', [RegistrationController::class, 'submitForm'])->name('registrations.submit');
```

### Dampak
- `route('registrations.form')` akan selalu menghasilkan `/join` (definisi terakhir menang)
- Link di halaman yang menggunakan `route('registrations.form')` mengarah ke `/join`, bukan `/daftar`
- Form submission via `/daftar` masih bekerja, tapi `route()` helper tidak konsisten

### Rekomendasi Perbaikan
```php
// Hapus route /daftar yang lama, atau beri nama berbeda:
Route::get('/daftar', [RegistrationController::class, 'showForm'])->name('registrations.form.legacy');
Route::post('/daftar', [RegistrationController::class, 'submitForm'])->name('registrations.submit.legacy');

// Atau: redirect /daftar ke /join
Route::redirect('/daftar', '/join', 301);
```

---

## BUG-06 — Perbandingan Status Siswa Tidak Konsisten di `processScan`

### Severitas
🟡 **Low** — Siswa aktif bisa ditolak scan, atau siswa nonaktif bisa lolos

### Lokasi
```
app/Http/Controllers/AttendanceController.php — processScan(), ~baris 95
```

### Kode Bermasalah
```php
// Di processScan: cek status sebagai string 'active'
if ($student->status !== 'active') {
    return response()->json([...], 403);
}
```

### Tapi di model Student:
```php
// Di database: status disimpan sebagai integer (0 = aktif, 1 = nonaktif)
// Di Student model:
public function scopeActive($query)
{
    return $query->where(function ($q) {
        $q->where('status', 0)->orWhere('status', '0')->orWhere('status', 'active');
    });
}

// Accessor statusShort: 0 → 'active', 1 → 'inactive'
public function getStatusShortAttribute()
{
    return $this->status === 1 ? 'inactive' : 'active';
}
```

### Analisis
Di database, `status` = `0` untuk siswa aktif (integer). Perbandingan `$student->status !== 'active'` akan selalu bernilai `true` karena `0 !== 'active'` di PHP (strict comparison). Artinya **semua siswa akan ditolak scan** dengan pesan "Status siswa tidak aktif".

Namun PHP type juggling di non-strict comparison (`!=`) bisa membuat `0 != 'active'` menjadi `false` dalam konteks tertentu. Ini adalah source of confusion.

### Rekomendasi Perbaikan
```php
// Gunakan accessor statusShort yang sudah ada, atau cek integer langsung:
if ($student->status !== 0 && $student->status !== '0') {
    return response()->json([
        'success' => false,
        'message' => 'Status siswa ' . $student->nama_lengkap . ' tidak aktif.'
    ], 403);
}

// Atau gunakan scope:
// $student = Student::active()->where('id_siswa', $scanCode)->first();
```

---

## BUG-07 — `stopImpersonateGuru` Bisa Menghapus Sesi Admin yang Benar

### Severitas
🟡 **Low** — UX bug, bisa menyebabkan admin ter-logout

### Lokasi
```
app/Http/Controllers/AuthController.php — stopImpersonateGuru()
```

### Kode Bermasalah
```php
public function stopImpersonateGuru(Request $request)
{
    $adminId = $request->session()->pull('impersonate_admin_id');
    $fromLoginPage = $request->session()->pull('quick_login_from_login_page');

    // BUG: jika admin login via quick_login, flag ini ada di sesi
    // dan stopImpersonateGuru akan logout si admin alih-alih kembali ke admin
    if ($fromLoginPage) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Kembali ke halaman login.');
    }
    // ...
}
```

### Analisis
Jika seorang admin login via quick-login (sehingga `quick_login_from_login_page = true` ada di session), lalu mencoba stop impersonate, sistem akan logout sepenuhnya daripada kembali ke akun admin.

`$request->session()->pull()` menghapus key dari session sekaligus mengambilnya — tapi karena keduanya dipull di awal, logika ini berjalan sebelum verifikasi kondisi yang tepat.

### Rekomendasi Perbaikan
```php
public function stopImpersonateGuru(Request $request)
{
    $adminId      = $request->session()->pull('impersonate_admin_id');
    $fromLoginPage = $request->session()->get('quick_login_from_login_page'); // jangan pull dulu

    // Kembali ke admin jika ada adminId yang valid
    if ($adminId) {
        $admin = User::find($adminId);
        if ($admin && $admin->isAdmin()) {
            Auth::login($admin, true);
            $request->session()->forget('quick_login_from_login_page');
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Kembali ke akun Administrator.');
        }
    }

    // Fallback: logout
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
}
```

---

## BUG-08 — Potensi Division by Zero di `PointController::index()`

### Severitas
🟡 **Low** — Ada guard, tapi guard tidak sempurna

### Lokasi
```
app/Http/Controllers/PointController.php — index(), ~baris 35
```

### Kode Bermasalah
```php
$totalPoints = $distributionQuery->sum('score') ?: 1; // Avoid division by zero

// Kemudian:
foreach ($distribution as $key => $data) {
    $distribution[$key]['percentage'] = round(($data['count'] / $totalPoints) * 100);
}
```

### Analisis
Guard `?: 1` menghindari division by zero ketika `sum('score') === 0`. Namun jika semua record di `points` table memiliki `score = 0` (misalnya karena input yang salah), `sum('score')` = `0` dan guard menggantinya dengan `1`. Persentase akan dihitung salah (misal `0/1 = 0%` untuk semua kategori) tapi tidak error.

Masalah lebih nyata: `$data['count']` bisa negatif jika ada poin violation dengan score negatif, dan `percentage` akan negatif.

### Rekomendasi Perbaikan
```php
// Gunakan abs() untuk memastikan percentage selalu positif
$distribution[$key]['percentage'] = $totalPoints > 0
    ? round((abs($data['count']) / abs($totalPoints)) * 100)
    : 0;
```

---

## BUG-09 — `checkAndRewardConsistentAttendance` adalah Fungsi Placeholder Kosong

### Severitas
🟢 **Info** — Tidak menyebabkan bug, tapi fitur reward tidak berfungsi

### Lokasi
```
app/Http/Controllers/AttendanceController.php — checkAndRewardConsistentAttendance()
```

### Kode Bermasalah
```php
private function checkAndRewardConsistentAttendance($student, $today, $academicYear)
{
    // Placeholder method for future implementation
    // Logic: Reward for consistent attendance can be added here
    return;
}
```

### Analisis
Komentar di `processScan` menyebut "Reward logic: -20 points if no late arrival for 4 months", tapi implementasinya tidak ada. Fungsi dipanggil di 4 tempat (processScan, store, update, bulkStore) tapi tidak melakukan apa-apa.

### Rekomendasi
Dokumentasikan dengan jelas bahwa ini adalah fitur yang belum diimplementasi, atau implementasikan sesuai spec.

---

## BUG-10 — Endpoint Ujian Masuk Bisa Diakses Tanpa Auth

### Severitas
🟢 **Info** — Ini kemungkinan by design (calon siswa belum punya akun), tapi perlu dikonfirmasi

### Lokasi
```
routes/web.php
Route::get('/exam/start/{id}', [ExamController::class, 'start'])->name('exams.start');
Route::post('/exam/submit/{id}', [ExamController::class, 'submit'])->name('exams.submit');
```

### Analisis
Ujian masuk (seleksi calon siswa) memang dirancang untuk diakses publik karena peserta belum memiliki akun. Namun tanpa proteksi:
- Siapa saja bisa submit jawaban ke endpoint `/exam/submit/{id}` berulang kali
- Tidak ada validasi bahwa yang submit adalah peserta yang valid (terdaftar di `registrations`)
- Score bisa dimanipulasi jika ada celah di validasi jawaban

### Rekomendasi
Konfirmasi apakah ini by design. Jika ya, pastikan:
1. Validasi `registration_id` ada di `ExamController::submit()`
2. Cek bahwa `ExamResult` belum ada sebelum submit (anti double-submit)
3. Pertimbangkan token berbasis `application_id` untuk membatasi akses

---

## Checklist Perbaikan

```
[ ] BUG-01: Pindahkan /clear-cache dan /show-logs ke dalam middleware auth + cek admin
[ ] BUG-02: Tambahkan rate-limiting dan signed URL untuk quick-login routes
[ ] BUG-03: Hash PIN siswa menggunakan cast 'hashed' + Hash::check() di perbandingan
[ ] BUG-04: Buat file app/Support/AttendanceReportHelper.php
[ ] BUG-05: Hapus atau rename route /daftar yang duplikat
[ ] BUG-06: Perbaiki perbandingan status di processScan() ke integer (0/1)
[ ] BUG-07: Perbaiki logika stopImpersonateGuru() agar tidak logout admin yang quick-login
[ ] BUG-08: Gunakan abs() untuk percentage di PointController
[ ] BUG-09: Implementasi checkAndRewardConsistentAttendance() atau dokumentasikan sebagai TODO
[ ] BUG-10: Pastikan anti double-submit di ExamController::submit()
```

---

## Factory yang Dibutuhkan untuk Test

Saat ini hanya ada `UserFactory`. Berikut factory tambahan yang diperlukan:

### `database/factories/StudentFactory.php`
```php
<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'id_siswa'     => 'SIS' . fake()->unique()->numerify('###'),
            'username'     => fake()->unique()->userName(),
            'pin'          => '123456',
            'status'       => 0,
            'nama_lengkap' => fake()->name(),
            'angkatan_id'  => null,
            'nisn'         => fake()->unique()->numerify('##########'),
            'no_nik'       => fake()->unique()->numerify('################'),
            'gender'       => fake()->randomElement([0, 1]),
            'mother_name'  => fake()->name('female'),
            'religion'     => 'Islam',
            'height'       => 165,
            'weight'       => 55,
            'place_of_birth'=> fake()->city(),
            'dateofbirth'  => fake()->date('Y-m-d', '-16 years'),
            'address'      => fake()->address(),
            'nis_siswa'    => fake()->unique()->numerify('####'),
            'role'         => 'student',
            'email'        => fake()->unique()->safeEmail(),
            'phone'        => fake()->phoneNumber(),
            'parent_phone' => fake()->phoneNumber(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 1]);
    }
}
```
