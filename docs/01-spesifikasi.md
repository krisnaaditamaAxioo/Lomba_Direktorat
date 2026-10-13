# 01 — Spesifikasi Produk Smart EduHub

**Tanggal:** 2026-08-07 
**Tim:** Smart EduHUb  
**Ketua:** Krisna Aditama  
**Anggota:** Najih Banyu Wicaksono, Mujahid Muhammad Abul Ghofur  
**Pembimbing:** Jeri Wilaga

---

## 1. Ringkasan Produk

**Smart EduHub** adalah Sistem Informasi Sekolah (SIS) berbasis web yang mengintegrasikan berbagai aktivitas akademik dan non-akademik sebuah SMK dalam satu platform. Sistem ini menangani pendaftaran siswa baru, manajemen data siswa/guru, absensi QR code, e-learning, ujian online, simpan-pinjam digital (cashless), raport industri, izin, pengaduan, serta portal mandiri untuk siswa dan orang tua/wali.

---

## 2. Tujuan

1. Mengotomatisasi proses administrasi sekolah yang selama ini manual.
2. Menyediakan akses informasi real-time bagi admin, guru, siswa, dan orang tua.
3. Meningkatkan transparansi dan akuntabilitas data sekolah.
4. Mendukung pembelajaran daring melalui modul dan ujian online.
5. Mewujudkan transaksi keuangan non-tunai (cashless) di lingkungan sekolah.

---

## 3. Peran Pengguna (User Roles)

| Peran | Akses Utama |
|-------|-------------|
| **Admin** | Semua fitur: master data, pendaftaran, keuangan, laporan, pengaturan sistem |
| **Guru / Staf** | Absensi, poin, modul, nilai, izin, notifikasi |
| **Teller** | Operasional keuangan harian: top-up saldo, tarik tunai, pembayaran, kelola kartu, approve request kartu. Landing: dashboard keuangan |
| **BUMS** (Badan Usaha Milik Sekolah) | Pengawasan & rekap transaksi keuangan untuk unit usaha sekolah. Landing: riwayat pembayaran |
| **Siswa** | Dashboard, modul, ujian, keuangan, izin, nilai, pengaduan |
| **Orang Tua / Wali** | Pantau anak (absensi, poin, keuangan, izin), ajukan izin/pengaduan |
| **Publik / Calon Siswa** | Formulir pendaftaran, cek status pendaftaran |

> **Catatan:** `Teller` dan `BUMS` memiliki izin keuangan yang sama dengan admin (melalui method `isFinanceStaff()` di model `User`). Perbedaannya terletak pada **landing page** setelah login: Teller diarahkan ke dashboard keuangan, sedangkan BUMS diarahkan ke riwayat pembayaran. Keduanya login melalui halaman login admin/guru (`/login`).

---

## 4. Fitur Fungsional

### 4.1 Pendaftaran Siswa Baru
- Formulir pendaftaran online (`/daftar`)
- Cek status pendaftaran (`/cek-status`)
- Verifikasi & persetujuan oleh admin (`/registrations`)
- Unduh informasi hasil seleksi (`/download-info`)
- Konversi data pendaftar menjadi data siswa

### 4.2 Manajemen Data Master
- CRUD siswa, guru/staf, orang tua
- Manajemen angkatan & tahun ajaran
- Cetak kartu siswa (QR code)
- Impor/ekspor data (Excel & PDF)
- Reset PIN siswa & password user

### 4.3 Absensi
- Input manual per siswa
- Input bulk (massal)
- Scan QR code (`/attendance-scan`)
- Laporan & ringkasan absensi
- Command otomatis tandai siswa tidak hadir

### 4.4 Poin Perilaku
- Pemberian poin prestasi/pelanggaran
- Aturan poin (point rules)
- Leaderboard siswa
- Lampiran foto bukti

### 4.5 E-Learning
- Mata pelajaran (courses)
- Modul (materi + soal)
- Pengumpulan & penilaian tugas
- Progress belajar siswa

### 4.6 Ujian Online
- Bank soal (manual & impor)
- Ujian online
- Rekap & ekspor nilai (Excel/PDF)
- Daftar nilai teratas

### 4.7 Keuangan / Simpan Pinjam Digital
- Kartu elektronik siswa
- Top-up & tarik tunai
- Transfer antar siswa (PIN)
- Pembayaran
- Rekap & ekspor transaksi
- Request kartu oleh siswa
- Dapat diakses oleh **Admin, Teller, dan BUMS** (izin keuangan setara)

### 4.8 Izin Siswa
- Pengajuan izin oleh siswa/orang tua
- Persetujuan oleh admin/guru

### 4.9 Pengaduan & Notifikasi
- Pengaduan siswa & orang tua
- Notifikasi per peran (siswa, orang tua, admin)

### 4.10 Portal Mandiri
- Portal siswa (`/siswa/...`)
- Portal orang tua/wali (`/wali/...`)

### 4.11 Pengaturan Sistem
- Tahun ajaran aktif
- Backup & reset data
- Hari libur

---

## 5. Fitur Non-Fungsional

| Aspek | Spesifikasi |
|-------|-------------|
| **Platform** | Web (responsive) |
| **Framework Backend** | Laravel 12 (PHP ^8.2) |
| **Framework Frontend** | Tailwind CSS v4, Vite 7, Blade |
| **Database** | MySQL 8.0 (utama), SQLite (testing) |
| **Autentikasi** | Session-based (admin/guru), guard terpisah (siswa, orang tua) |
| **Keamanan** | Hashing password (Bcrypt), PIN untuk siswa, middleware auth |
| **Ekspor** | Excel (Maatwebsite), PDF (DomPDF) |
| **QR Code** | SimpleSoftwareIO QR Code |

---

## 6. Batasan

1. Pembayaran gateway otomatis belum terintegrasi (top-up manual admin).
2. Notifikasi real-time berbasis polling/refresh, belum WebSocket.
3. Belum ada fitur chat/diskusi dalam modul.
4. Kalender akademik terintegrasi penuh belum ada (baru hari libur).
5. Belum ada aplikasi mobile native (hanya web responsif).
6. Penjadwalan pelajaran otomatis belum diimplementasikan.

---

## 7. Prasyarat Teknis

- PHP ^8.2
- Composer 2.x
- Node.js 20.x
- NPM 10.x
- MySQL 8.0
- Browser modern (Chrome/Edge/Firefox)
