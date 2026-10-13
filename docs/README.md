# Dokumentasi Smart EduHub

> **Tim Smart EduHUb** — Lomba Direktorat Jenderal Pendidikan Vokasi

Folder ini berisi seluruh dokumentasi teknis dan non-teknis pengembangan **Smart EduHub**, sistem informasi sekolah (SIS) berbasis web yang dibangun dengan **Laravel 12**.

---

## Daftar Isi

1. [Tentang Proyek](#1-tentang-proyek)
2. [Navigasi Dokumen](#2-navigasi-dokumen)
3. [Ringkasan Aplikasi](#3-ringkasan-aplikasi)
4. [Stack Teknologi](#4-stack-teknologi)
5. [Arsitektur Sistem](#5-arsitektur-sistem)
6. [Alur Pengembangan (AI-Assisted)](#6-alur-pengembangan-ai-assisted)
7. [Modul & Fitur](#7-modul--fitur)
8. [Peran Pengguna](#8-peran-pengguna)
9. [Database & Model](#9-database--model)
10. [Testing & Kualitas Kode](#10-testing--kualitas-kode)
11. [Bug yang Ditemukan & Status Perbaikan](#11-bug-yang-ditemukan--status-perbaikan)
12. [Change Request & Keputusan Teknis](#12-change-request--keputusan-teknis)
13. [Tim Pengembang](#13-tim-pengembang)

---

## 1. Tentang Proyek

**Smart EduHub** adalah Sistem Informasi Sekolah (SIS) berbasis web yang mengintegrasikan seluruh kebutuhan administrasi dan akademik SMK dalam satu platform. Proyek ini dikembangkan untuk **Lomba Direktorat Jenderal Pendidikan Vokasi** oleh Tim Smart EduHUb.

| Atribut | Detail |
|---------|--------|
| **Nama Produk** | Smart EduHub |
| **Nama Tim** | Smart EduHUb |
| **Framework** | Laravel 12 (PHP ^8.2) |
| **Database** | MySQL 8.0 (produksi), SQLite in-memory (testing) |
| **Frontend** | Tailwind CSS v4, Vite 7, Blade, Alpine.js |
| **Testing** | PHPUnit 11 |
| **Jumlah Modul** | 12 modul fitur utama |
| **Jumlah Controller** | 27 controller |
| **Jumlah Model** | 33 model Eloquent |
| **Jumlah Route** | 100+ route (web.php) |
| **Jumlah Migration** | 65+ file migrasi |
| **Auth Guard** | 3 guard: `web` (admin/guru), `student` (siswa), `parent` (orang tua) |

---

## 2. Navigasi Dokumen

| File | Isi |
|------|-----|
| [`01-spesifikasi.md`](01-spesifikasi.md) | Spesifikasi produk lengkap: tujuan, peran, fitur fungsional & non-fungsional, batasan, prasyarat teknis |
| [`02-rencana.md`](02-rencana.md) | Rencana pengembangan: metodologi iteratif-incremental, 13 fase, timeline, pembagian tugas tim, stack teknologi, dan rencana testing |
| [`03-log-prompt.md`](03-log-prompt.md) | Log seluruh prompt AI (21 prompt): Gemini → Stitch → Laravel → Trae, beserta input/output tiap prompt dan catatan penggunaan AI |
| [`04-kasus-uji.md`](04-kasus-uji.md) | 60+ kasus uji manual: autentikasi, pendaftaran, absensi, e-learning, ujian, keuangan, poin, izin, pengaduan, portal orang tua, pengaturan sistem |
| [`05-kesalahan-ai.md`](05-kesalahan-ai.md) | 17 catatan kesalahan AI selama pengembangan beserta cara penanganannya dan pelajaran yang dipetik |
| [`06-catatan-review.md`](06-catatan-review.md) | Catatan review kode dan desain per fase pengembangan |
| [`07-refleksi.md`](07-refleksi.md) | Refleksi tim: apa yang berjalan baik, tantangan, pelajaran, hal yang akan dilakukan berbeda, dan roadmap ke depan |
| [`08-penjelasan-kode.md`](08-penjelasan-kode.md) | Penjelasan kode teknis: arsitektur, pola desain, komponen kunci, dan keputusan implementasi |
| [`analisis-change-request.md`](analisis-change-request.md) | Analisis 8 change request: latar belakang, alasan, ruang lingkup, file yang diubah, dan verifikasi |
| [`bukti/README.md`](bukti/README.md) | Panduan tangkapan layar alur inti: daftar 14 screenshot yang harus diambil beserta keterangan |

---

## 3. Ringkasan Aplikasi

Smart EduHub menangani seluruh siklus kegiatan administrasi dan akademik sekolah:

```
Calon Siswa  ──→  Pendaftaran  ──→  Verifikasi Admin  ──→  Siswa Aktif
                                                                │
                    ┌───────────────────────────────────────────┤
                    │                                           │
             Portal Siswa                               Portal Orang Tua
          ┌─────────┴──────────┐                    ┌──────────┴──────────┐
          │  E-Learning        │                    │  Pantau Absensi     │
          │  Ujian Online      │                    │  Pantau Poin        │
          │  Absensi QR        │                    │  Pantau Keuangan    │
          │  Dompet Digital    │                    │  Ajukan Izin        │
          │  Poin & Prestasi   │                    │  Kirim Pengaduan    │
          │  Izin              │                    └─────────────────────┘
          │  Notifikasi        │
          └────────────────────┘
                    │
             Admin / Guru / Teller / BUMS
          ┌─────────┴───────────────────┐
          │  Master Data                │
          │  Keuangan (kartu e-wallet)  │
          │  Laporan & Ekspor           │
          │  Pengaturan Sistem          │
          └─────────────────────────────┘
```

### URL Utama

| Halaman | URL |
|---------|-----|
| Landing Page | `http://localhost:8000/` |
| Login Admin/Guru/Teller/BUMS | `http://localhost:8000/login` |
| Login Siswa | `http://localhost:8000/siswa/login` |
| Login Orang Tua/Wali | `http://localhost:8000/wali/login` |
| Pendaftaran Siswa Baru | `http://localhost:8000/daftar` |
| Cek Status Pendaftaran | `http://localhost:8000/cek-status` |
| Scan Absensi QR | `http://localhost:8000/attendance-scan` |

---

## 4. Stack Teknologi

### Backend
| Teknologi | Versi | Kegunaan |
|-----------|-------|---------|
| PHP | ^8.2 | Bahasa pemrograman utama |
| Laravel | 12.x | Framework MVC |
| MySQL | 8.0 | Database produksi |
| SQLite | 3.x | Database testing (in-memory) |
| DomPDF | ^3.0 | Generate file PDF |
| Maatwebsite Excel | ^3.1 | Import/export file Excel |
| SimpleSoftwareIO QR Code | ^4.2 | Generate QR code siswa |
| Google API Client | 2.18 | Integrasi Google services |
| PHPUnit | ^11.5 | Framework testing |

### Frontend
| Teknologi | Versi | Kegunaan |
|-----------|-------|---------|
| Tailwind CSS | v4 | Utility-first CSS framework |
| Vite | 7.x | Build tool & dev server |
| Alpine.js | 3.x | JavaScript interaktivitas ringan |
| Blade | Laravel built-in | Template engine |
| Material Symbols | Google Fonts | Ikon UI |
| Inter & Plus Jakarta Sans | Google Fonts | Tipografi |

### Tooling
| Alat | Kegunaan |
|------|---------|
| Composer 2.x | Manajemen dependensi PHP |
| Node.js 20.x + NPM 10.x | Manajemen dependensi JS & build |
| Git | Version control |
| Laragon | Local development environment (Windows) |

---

## 5. Arsitektur Sistem

### Pola Arsitektur
Aplikasi menggunakan pola **MVC (Model-View-Controller)** bawaan Laravel dengan beberapa layer tambahan:

```
Request HTTP
     │
     ▼
routes/web.php          ← Mendefinisikan semua URL dan guard middleware
     │
     ▼
Middleware               ← Auth guard, CheckMaintenanceMode, SetLocale
     │
     ▼
Controller               ← Logika bisnis, validasi, orkestrasi data
     │         │
     ▼         ▼
  Model      Export/Import    ← Eloquent ORM, relasi, scope, accessor
     │
     ▼
Database (MySQL/SQLite)
     │
     ▼
View (Blade)             ← Template rendering, layout, komponen
     │
     ▼
Response HTTP
```

### Sistem Autentikasi Multi-Guard

Aplikasi menggunakan **3 guard terpisah** dengan model berbeda:

| Guard | Model | Driver | Tabel | Digunakan Untuk |
|-------|-------|--------|-------|-----------------|
| `web` (default) | `User` | session | `users` | Admin, Guru, Teller, BUMS, Wali Kelas, Wakasis, BK |
| `student` | `Student` | session | `students` | Portal Siswa |
| `parent` | `ParentUser` | session | `parents` | Portal Orang Tua/Wali |

Login URL berbeda per guard:
- `/login` → guard `web`
- `/siswa/login` → guard `student`
- `/wali/login` → guard `parent`

### Struktur Peran di Guard `web`

```
web guard (User model)
├── admin        → Akses penuh
├── guru         → E-learning, poin, absensi terbatas
├── wali_kelas   → Absensi, laporan, poin
├── wakasis      → Poin, laporan kesiswaan
├── bk           → Poin, konseling
├── teller       → Keuangan (landing: dashboard keuangan)
└── bums         → Keuangan read/approve (landing: riwayat pembayaran)
```

---

## 6. Alur Pengembangan (AI-Assisted)

Proyek ini dikembangkan menggunakan pendekatan **AI-assisted iteratif** dengan alur:

```
Gemini AI
   │  (membuat prompt untuk tampilan)
   ▼
Stitch (Google)
   │  (membuat desain UI dari prompt)
   ▼
resources/views/ (Laravel)
   │  (kode frontend HTML/CSS disalin manual)
   ▼
Trae AI (per fase, Fase 1–13)
   │  (membuat MVC + database MySQL per modul)
   ▼
Kiro AI (Fase 13 — Testing & Finalisasi)
   │  (testing, bug hunting, dokumentasi, perbaikan)
   ▼
Aplikasi Selesai
```

### Fase Pengembangan (13 Fase)

| Fase | Modul | Status |
|------|-------|--------|
| Fase 1 | Setup proyek, database, autentikasi multi-peran | ✅ Selesai |
| Fase 2 | Master data (siswa, guru, angkatan, tahun ajaran) | ✅ Selesai |
| Fase 3 | Pendaftaran siswa baru & status | ✅ Selesai |
| Fase 4 | Absensi (manual, bulk, scan QR) & laporan | ✅ Selesai |
| Fase 5 | Poin perilaku & leaderboard | ✅ Selesai |
| Fase 6 | E-learning (mapel, modul, soal, tugas) | ✅ Selesai |
| Fase 7 | Ujian online & rekap nilai | ✅ Selesai |
| Fase 8 | Keuangan / simpan-pinjam digital | ✅ Selesai |
| Fase 9 | Izin siswa | ✅ Selesai |
| Fase 10 | Pengaduan & notifikasi | ✅ Selesai |
| Fase 11 | Portal siswa & portal orang tua | ✅ Selesai |
| Fase 12 | Pengaturan sistem, multi-bahasa, polishing | ✅ Selesai |
| Fase 13 | Testing, dokumentasi, finalisasi | 🔄 Berjalan |

Detail lengkap log prompt AI: lihat [`03-log-prompt.md`](03-log-prompt.md)

---

## 7. Modul & Fitur

### ✅ Fitur yang Sudah Diimplementasi

| # | Modul | Fitur Utama |
|---|-------|-------------|
| 1 | **Pendaftaran Siswa Baru** | Formulir online, cek status, verifikasi admin, unduh info, konversi ke data siswa |
| 2 | **Master Data** | CRUD siswa/guru/orang tua, angkatan, tahun ajaran, cetak kartu QR, import/export Excel & PDF |
| 3 | **Absensi** | Input manual, bulk massal, scan QR code, laporan & ringkasan, auto-mark absent (cron) |
| 4 | **Poin Perilaku** | Poin prestasi & pelanggaran, aturan poin otomatis, leaderboard, lampiran foto |
| 5 | **E-Learning** | Mata pelajaran, modul (teks/file), soal & tugas, penilaian tugas, progress belajar siswa |
| 6 | **Ujian Online** | Bank soal (manual & import), ujian online, rekap & export nilai Excel/PDF |
| 7 | **Keuangan Digital** | Kartu e-wallet siswa, top-up, tarik tunai, transfer antar siswa (PIN), pembayaran, rekap & export |
| 8 | **Izin Siswa** | Pengajuan oleh siswa/orang tua, persetujuan admin/guru, bukti foto |
| 9 | **Pengaduan & Notifikasi** | Pengaduan polymorphic (siswa/orang tua), notifikasi per peran, polling real-time |
| 10 | **Portal Siswa** | Dashboard mandiri, akses semua fitur siswa dari mobile-first UI |
| 11 | **Portal Orang Tua/Wali** | Pantau anak, ajukan izin/pengaduan, akses data keuangan & absensi anak |

### ⚠️ Batasan / Belum Diimplementasi

| Batasan | Keterangan |
|---------|------------|
| Payment gateway otomatis | Top-up masih manual oleh admin |
| Notifikasi real-time | Berbasis polling HTTP, belum WebSocket |
| Chat/diskusi modul | Belum ada fitur diskusi siswa-guru dalam modul |
| Kalender akademik penuh | Baru tersedia manajemen hari libur |
| Aplikasi mobile native | Hanya web responsif (mobile-first) |
| Penjadwalan pelajaran otomatis | Belum diimplementasi |
| Analitik prediktif | Belum ada dashboard analitik lanjutan |

---

## 8. Peran Pengguna

| Peran | Guard | Landing Page | Akses Utama |
|-------|-------|-------------|-------------|
| **Admin** | `web` | `/dashboard` | Semua fitur: master data, keuangan, laporan, pengaturan |
| **Guru** | `web` | `/dashboard/guru` | E-learning, poin, absensi terbatas |
| **Wali Kelas** | `web` | `/dashboard` | Absensi, laporan, poin, izin |
| **Wakasis** | `web` | `/dashboard` | Poin, laporan kesiswaan |
| **BK** | `web` | `/dashboard` | Poin, data siswa |
| **Teller** | `web` | `/finance/dashboard` | Operasional keuangan harian (topup, bayar, approve kartu) |
| **BUMS** | `web` | `/finance/payments/history` | Rekap & riwayat keuangan unit usaha |
| **Siswa** | `student` | `/siswa/dashboard` | Portal mandiri: belajar, ujian, keuangan, absensi, poin |
| **Orang Tua/Wali** | `parent` | `/wali/dashboard` | Pantau anak, ajukan izin/pengaduan |
| **Publik** | — | `/` | Formulir pendaftaran, cek status |

> **Catatan Teller & BUMS:** Keduanya memiliki izin keuangan yang sama dengan admin (method `isFinanceStaff()` di model `User` mengembalikan `true` untuk role `admin`, `teller`, dan `bums`). Perbedaan hanya pada halaman yang ditampilkan setelah login (`dashboardRouteFor()` di `AuthController`).

---

## 9. Database & Model

### Tabel Utama

| Tabel | Model | Keterangan |
|-------|-------|------------|
| `users` | `User` | Semua staff (admin, guru, teller, bums, dll.) |
| `students` | `Student` | Data siswa + auth portal siswa |
| `parents` | `ParentUser` | Orang tua/wali + auth portal wali |
| `angkatans` | `Angkatan` | Rombel/kelas (kode, nama, tahun masuk) |
| `academic_years` | `AcademicYear` | Tahun ajaran (nama, is_active) |
| `academic_year_student` | `AcademicYearStudent` | Pivot: enrollment siswa per tahun ajaran |
| `registrations` | `Registration` | Data calon siswa yang mendaftar |
| `attendances` | `Attendance` | Rekam kehadiran harian |
| `points` | `Point` | Poin prestasi/pelanggaran siswa |
| `point_rules` | `PointRule` | Aturan pemberian poin otomatis |
| `courses` | `Course` | Mata pelajaran/kursus |
| `modules` | `Module` | Modul dalam kursus |
| `module_progress` | `ModuleProgress` | Progress baca modul per siswa |
| `assignment_submissions` | `AssignmentSubmission` | Pengumpulan tugas siswa |
| `exam_questions` | `ExamQuestion` | Bank soal ujian masuk |
| `exam_results` | `ExamResult` | Hasil ujian masuk |
| `student_permits` | `StudentPermit` | Izin/sakit siswa |
| `student_cards` | `StudentCard` | Kartu e-wallet siswa |
| `student_card_requests` | `StudentCardRequest` | Pengajuan pembuatan kartu |
| `student_card_transactions` | `StudentCardTransaction` | Transaksi e-wallet (topup/bayar/transfer/tarik) |
| `complaints` | `Complaint` | Pengaduan polymorphic (siswa/orang tua) |
| `student_notifications` | `StudentNotification` | Notifikasi untuk siswa |
| `admin_notifications` | `AdminNotification` | Notifikasi untuk admin/staff |
| `parent_notifications` | `ParentNotification` | Notifikasi untuk orang tua |
| `holidays` | `Holiday` | Hari libur (special/monthly/range) |
| `settings` | `Setting` | Konfigurasi global key-value |

### Relasi Kunci

```
Student  ──hasmany──  Attendance
         ──hasmany──  Point
         ──hasmany──  StudentCard ──hasmany── StudentCardTransaction
         ──hasmany──  StudentPermit
         ──hasmany──  ModuleProgress
         ──hasmany──  AssignmentSubmission
         ──hasmany──  StudentNotification
         ──hasmany──  ParentUser (orang tua)
         ──belongstomany── AcademicYear (via academic_year_student)

Course  ──hasmany──  Module ──hasmany── ModuleProgress
                            ──hasmany── AssignmentSubmission
                            ──hasmany── ModuleQuestion

Complaint (polymorphic: complainant = Student atau User)
```

---

## 10. Testing & Kualitas Kode

### Konfigurasi Testing

- **Framework:** PHPUnit 11
- **Database:** SQLite in-memory (`:memory:`)
- **Environment:** `APP_ENV=testing`, cache/session/queue = array

### Cara Menjalankan Test

```bash
# Masuk ke folder aplikasi
cd smart-acp

# Gunakan PHP dari Laragon (jika php tidak ada di PATH)
C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test

# Atau jika php sudah di PATH:
php artisan test

# Hanya unit test
php artisan test --testsuite=Unit

# Hanya feature test
php artisan test --testsuite=Feature

# Satu file spesifik
php artisan test tests/Feature/AuthTest.php

# Stop di kegagalan pertama
php artisan test --stop-on-failure
```

### File Test yang Ada

#### Unit Tests (`tests/Unit/`)

| File | Yang Diuji | Jumlah Test |
|------|-----------|-------------|
| `AttendanceReportHelperTest.php` | `App\Support\AttendanceReportHelper::summarizePeriod()` | 7 test |
| `UserModelTest.php` | Role methods, finance permissions, roleLabel accessor | 10 test |
| `StudentModelTest.php` | Accessor gender/status, nama, bug dokumentasi status integer | 12 test |
| `HolidayModelTest.php` | `checkHoliday()`, `getHolidayName()`, tipe special/range/monthly | 11 test |
| `SettingModelTest.php` | `get()`, `set()`, update tanpa duplikat | 7 test |

#### Feature Tests (`tests/Feature/`)

| File | Yang Diuji | Jumlah Test |
|------|-----------|-------------|
| `AuthTest.php` | Login admin/guru, redirect per role, logout, impersonate | 13 test |
| `AttendanceScanTest.php` | Scan hadir/terlambat, duplicate, holiday, siswa nonaktif | 10 test |
| `PointTest.php` | CRUD poin, notifikasi otomatis, validasi, auth | 10 test |
| `StudentPortalAuthTest.php` | Login siswa PIN, guard isolation, logout | 10 test |
| `StudentFinanceTransferTest.php` | Transfer e-wallet, PIN, saldo, akses silang | 15 test |
| `AttendancePenaltyTest.php` | Auto poin dari PointRule, tidak dobel per bulan | 5 test |
| `SecurityTest.php` | Bug route tanpa auth, impersonate, guard isolation | 9 test |
| `ModuleAndParentAccessTest.php` | Guru hanya lihat modul sendiri, akses halaman parents | 3 test |

### Hasil Test Terkini

```
Tests: 124 passed (295 assertions)
Duration: ~8 seconds
```

Detail lengkap: lihat [`unit-test-plan.md`](../smart-acp/docs/unit-test-plan.md)

---

## 11. Bug yang Ditemukan & Status Perbaikan

Analisis dilakukan melalui static code analysis, route tracing, dan logic review pada Oktober 2026.

| ID | Severitas | Judul | Status |
|----|-----------|-------|--------|
| BUG-01 | 🔴 High | Route `/clear-cache` & `/show-logs` bisa diakses publik tanpa auth | ⚠️ Terdokumentasi |
| BUG-02 | 🔴 High | Quick login tanpa password tidak ada proteksi rate-limit di server | ⚠️ Terdokumentasi |
| BUG-03 | 🟠 Medium | PIN siswa disimpan dan dibandingkan sebagai plain text | ⚠️ Terdokumentasi |
| BUG-04 | 🟠 Medium | Class `App\Support\AttendanceReportHelper` tidak ada meski test sudah ada | ✅ Diperbaiki |
| BUG-05 | 🟡 Low | Nama route `registrations.form` didefinisikan dua kali (duplikat) | ⚠️ Terdokumentasi |
| BUG-06 | 🟡 Low | `processScan()` cek `status !== 'active'` tapi DB simpan integer `0/1` | ✅ Diperbaiki |
| BUG-07 | 🟡 Low | `stopImpersonateGuru()` bisa logout admin yang masuk via quick-login | ⚠️ Terdokumentasi |
| BUG-08 | 🟡 Low | Percentage poin bisa negatif jika score negatif | ⚠️ Terdokumentasi |
| BUG-09 | 🟢 Info | `checkAndRewardConsistentAttendance()` adalah fungsi placeholder kosong | ⚠️ Terdokumentasi |
| BUG-10 | 🟢 Info | Endpoint ujian masuk bisa diakses tanpa autentikasi, tanpa anti double-submit | ⚠️ Terdokumentasi |

Detail lengkap beserta kode perbaikan: lihat [`bug-hunting.md`](../smart-acp/docs/bug-hunting.md)

**Perbaikan yang sudah diterapkan ke kode:**
- **BUG-04:** File `app/Support/AttendanceReportHelper.php` dibuat
- **BUG-06:** `AttendanceController::processScan()` diubah dari `$student->status !== 'active'` menjadi `$student->status !== 0 && $student->status !== '0'`
- **Migration fix:** 3 migration yang pakai `ALTER TABLE ... MODIFY COLUMN` (MySQL-only) diperbarui agar skip di SQLite saat testing

---

## 12. Change Request & Keputusan Teknis

### Change Request Selama Pengembangan

| ID | Judul | Dampak | Status |
|----|-------|--------|--------|
| CR-01 | Ganti istilah "PPDB" → "Pendaftaran Anggota/Siswa Baru" | Dokumentasi | ✅ Selesai |
| CR-02 | Ganti nama produk "Smart Axioo" → "Smart EduHub" | Dokumentasi | ✅ Selesai |
| CR-03 | Perincian struktur folder aplikasi di README | Dokumentasi | ✅ Selesai |
| CR-04 | Seragamkan tampilan Stitch menjadi layout Blade | Tampilan (View) | ✅ Selesai |
| CR-05 | Samakan nama route, view, dan controller | Kode | ✅ Selesai |
| CR-06 | Samakan nama kolom database dengan field tampilan | Database, View | ✅ Selesai |
| CR-07 | Perbaiki urutan migrasi dan relasi antartabel | Database | ✅ Selesai |
| CR-08 | Sesuaikan konfigurasi middleware dengan Laravel 12 | Kode | ✅ Selesai |

Detail lengkap: lihat [`analisis-change-request.md`](analisis-change-request.md)

### Keputusan Teknis Penting

| Keputusan | Alasan |
|-----------|--------|
| 3 auth guard terpisah | Tabel berbeda (`users`, `students`, `parents`), atribut berbeda (PIN vs password), URL login berbeda |
| Status siswa sebagai integer (0/1) bukan string | Konsisten dengan boolean di MySQL, scope `active()` lebih fleksibel |
| SQLite untuk testing | Lebih cepat, tanpa setup server, sudah diaktifkan di PHP Laragon |
| Polling notifikasi (bukan WebSocket) | Tidak butuh server tambahan (Pusher/Redis), cocok untuk skala sekolah |
| PIN disimpan plain text (diketahui sebagai bug) | Keputusan awal untuk kesederhanaan; harus di-hash di produksi |
| `Setting` model sebagai key-value store | Fleksibel untuk konfigurasi dinamis tanpa migration baru |

---

## 13. Tim Pengembang

| Posisi | Nama |
|--------|------|
| **Nama Tim** | Smart EduHUb |
| **Ketua Tim** | Krisna Aditama |
| **Anggota 1** | Najih Banyu Wicaksono |
| **Anggota 2** | Mujahid Muhammad Abul Ghofur |
| **Pembimbing** | Jeri Wilaga |
| **Event** | Lomba Direktorat Jenderal Pendidikan Vokasi |

### Pembagian Fokus Modul

| Anggota | Fokus |
|---------|-------|
| Krisna Aditama | Arsitektur sistem, autentikasi multi-guard, modul keuangan, integrasi |
| Najih Banyu Wicaksono | Pendaftaran siswa baru, absensi, ujian online, poin perilaku |
| Mujahid Muhammad Abul Ghofur | E-learning, izin siswa, portal siswa & orang tua |

---

## Struktur Folder `docs/`

```
docs/
├── README.md                    ← File ini — indeks & ringkasan dokumentasi
├── 01-spesifikasi.md            ← Spesifikasi produk lengkap
├── 02-rencana.md                ← Rencana pengembangan & timeline
├── 03-log-prompt.md             ← Log 21 prompt AI (Gemini, Trae, Kiro)
├── 04-kasus-uji.md              ← 60+ kasus uji manual
├── 05-kesalahan-ai.md           ← 17 catatan kesalahan AI & penanganannya
├── 06-catatan-review.md         ← Catatan review kode per fase
├── 07-refleksi.md               ← Refleksi tim & roadmap selanjutnya
├── 08-penjelasan-kode.md        ← Penjelasan teknis kode & arsitektur
├── analisis-change-request.md   ← Analisis 8 change request
└── bukti/
    └── README.md                ← Panduan 14 screenshot alur inti
```

---

*© 2026 Tim Smart EduHUb — Lomba Direktorat Jenderal Pendidikan Vokasi*
