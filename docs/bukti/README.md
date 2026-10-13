# Bukti Tangkapan Layar — Smart EduHub

> **Tim Smart EduHub** — Lomba Direktorat Jenderal Pendidikan Vokasi

Dokumen ini menginventarisasi berkas yang benar-benar tersedia di folder `Screenshot/`. Nama berkas dipertahankan seperti aslinya. Status **Ada** berarti berkas dengan nama tersebut ditemukan; status itu tidak menyatakan bahwa alur atau hasil pengujian tertentu sudah tervalidasi.

## Daftar Isi

1. [Cara Mengambil Screenshot](#1-cara-mengambil-screenshot)
2. [Akun untuk Screenshot](#2-akun-untuk-screenshot)
3. [Pendaftaran Siswa Baru](#3-pendaftaran-siswa-baru)
4. [Portal Siswa dan E-Learning](#4-portal-siswa-dan-e-learning)
5. [Absensi](#5-absensi)
6. [Portal Orang Tua/Wali](#6-portal-orang-tuawali)
7. [Modul Keuangan](#7-modul-keuangan)
8. [Dashboard Admin](#8-dashboard-admin)
9. [Bug Hunting](#9-bug-hunting)
10. [Bukti Pengujian](#10-bukti-pengujian)
11. [Screenshot Berukuran Mobile](#11-screenshot-berukuran-mobile)
12. [Ringkasan Status Bukti](#12-ringkasan-status-bukti)

## 1. Cara Mengambil Screenshot

### Prasyarat

Jalankan aplikasi terlebih dahulu:

```bash
cd smart-acp
php artisan serve
```

Buka aplikasi di `http://localhost:8000`.

### Format dan lokasi

- Format yang disarankan: PNG.
- Simpan bukti di `docs/bukti/Screenshot/`.
- Tangkapan layar yang ada memiliki lebar 393 px (mobile) atau sekitar 2560 px (desktop); ukuran berkas bukan bukti bahwa alurnya telah diuji.

## 2. Akun untuk Screenshot

| Peran | Username / Email | Password / PIN | URL Login |
|-------|-----------------|----------------|-----------|
| **Admin** | `admin@smartaxioo.com` | `password` | `/login` |
| **Guru** | Buat via menu Management Users | `password` | `/login/guru` |
| **Teller** | Buat via menu Management Users (role: teller) | `password` | `/login` |
| **BUMS** | Buat via menu Management Users (role: bums) | `password` | `/login` |
| **Siswa** | `siswa123` | PIN: `1234` | `/siswa/login` |
| **Orang Tua** | Daftar via `/wali/register` | password pilihan | `/wali/login` |

## 3. Pendaftaran Siswa Baru

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Halaman utama | [Smart EduHub.png](<Screenshot/Smart EduHub.png>), [Smart EduHub (1).png](<Screenshot/Smart EduHub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Formulir pendaftaran | [Pendaftaran Siswa Baru - Smart Axioo.png](<Screenshot/Pendaftaran Siswa Baru - Smart Axioo.png>), [Pendaftaran Siswa Baru - Smart Axioo (1).png](<Screenshot/Pendaftaran Siswa Baru - Smart Axioo (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Pelacakan/proses pendaftaran | [Proses Pendaftaran - Smart Axioo.png](<Screenshot/Proses Pendaftaran - Smart Axioo.png>), [Proses Pendaftaran - Smart Axioo (1).png](<Screenshot/Proses Pendaftaran - Smart Axioo (1).png>) | ✅ Ada — bukti tampilan proses; belum membuktikan hasil diterima/ditolak |
| Daftar pendaftaran di admin | [Smart Edu Hub - Student Registrations.png](<Screenshot/Smart Edu Hub - Student Registrations.png>), [Smart Edu Hub - Student Registrations (1).png](<Screenshot/Smart Edu Hub - Student Registrations (1).png>) | ✅ Ada |


## 4. Portal Siswa dan E-Learning

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Login siswa | [Login Siswa - Smart EduHub.png](<Screenshot/Login Siswa - Smart EduHub.png>), [Login Siswa - Smart EduHub (1).png](<Screenshot/Login Siswa - Smart EduHub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Dashboard siswa | [Dashboard Siswa - Smart Edu Hub.png](<Screenshot/Dashboard Siswa - Smart Edu Hub.png>), [Dashboard Siswa - Smart Edu Hub (1).png](<Screenshot/Dashboard Siswa - Smart Edu Hub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Kursus siswa | [Kursus Saya - Smart Edu Hub.png](<Screenshot/Kursus Saya - Smart Edu Hub.png>) | ✅ Ada |
| Modul dan materi | [Modul & Materi - Smart Edu Hub.png](<Screenshot/Modul & Materi - Smart Edu Hub.png>) | ✅ Ada |
| Keuangan siswa | [Keuangan - Smart Edu Hub.png](<Screenshot/Keuangan - Smart Edu Hub.png>), [Keuangan - Smart Edu Hub (1).png](<Screenshot/Keuangan - Smart Edu Hub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Transfer saldo | [Transfer Saldo - Smart Edu Hub.png](<Screenshot/Transfer Saldo - Smart Edu Hub.png>), [Transfer Saldo - Smart Edu Hub (1).png](<Screenshot/Transfer Saldo - Smart Edu Hub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Ujian online dan hasil ujian | — | ⬜ Belum ada bukti |

## 5. Absensi

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Terminal absensi | [Attendance Terminal - Smart Axioo.png](<Screenshot/Attendance Terminal - Smart Axioo.png>) | ✅ Ada — tampilan terminal/scanner |
| Kartu siswa dengan QR | [Cetak Kartu Siswa - Smart Edu Hub.png](<Screenshot/Cetak Kartu Siswa - Smart Edu Hub.png>) | ✅ Ada |
| Laporan absensi bulanan | [Smart Edu Hub - Laporan Absensi Bulanan.png](<Screenshot/Smart Edu Hub - Laporan Absensi Bulanan.png>), [Smart Edu Hub - Laporan Absensi Bulanan (1).png](<Screenshot/Smart Edu Hub - Laporan Absensi Bulanan (1).png>), [Smart Edu Hub - Laporan Absensi Bulanan (2).png](<Screenshot/Smart Edu Hub - Laporan Absensi Bulanan (2).png>) | ✅ Ada — tiga berkas |
| Hasil scan yang menunjukkan absensi berhasil | — | ⬜ Belum ada bukti yang dapat diidentifikasi dari nama berkas |

## 6. Portal Orang Tua/Wali

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Login wali | [Login Wali Murid - Smart EduHub.png](<Screenshot/Login Wali Murid - Smart EduHub.png>) | ✅ Ada |
| Dashboard wali | [Dashboard Wali Murid - Smart Edu Hub.png](<Screenshot/Dashboard Wali Murid - Smart Edu Hub.png>), [Dashboard Wali Murid - Smart Edu Hub (1).png](<Screenshot/Dashboard Wali Murid - Smart Edu Hub (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Presensi anak | [Presensi Anak - Smart Edu Hub.png](<Screenshot/Presensi Anak - Smart Edu Hub.png>), [Presensi Anak - Smart Edu Hub (1).png](<Screenshot/Presensi Anak - Smart Edu Hub (1).png>) | ✅ Ada — dua ukuran tangkapan |

## 7. Modul Keuangan

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Dashboard keuangan | [Smart Edu Hub -.png](<Screenshot/Smart Edu Hub -.png>), [Smart Edu Hub - (1).png](<Screenshot/Smart Edu Hub - (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Pengajuan kartu | [Smart Edu Hub - Pengajuan Kartu.png](<Screenshot/Smart Edu Hub - Pengajuan Kartu.png>), [Smart Edu Hub - Pengajuan Kartu (1).png](<Screenshot/Smart Edu Hub - Pengajuan Kartu (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Daftar kartu | [Smart Edu Hub - Daftar Kartu.png](<Screenshot/Smart Edu Hub - Daftar Kartu.png>), [Smart Edu Hub - Daftar Kartu (1).png](<Screenshot/Smart Edu Hub - Daftar Kartu (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Rekap laporan keuangan | [Smart Edu Hub - Rekap Laporan Keuangan.png](<Screenshot/Smart Edu Hub - Rekap Laporan Keuangan.png>), [Smart Edu Hub - Rekap Laporan Keuangan (1).png](<Screenshot/Smart Edu Hub - Rekap Laporan Keuangan (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Bukti transaksi top-up tertentu | — | ⬜ Belum ada bukti khusus yang dapat diidentifikasi |

## 8. Dashboard Admin

| Bukti | Berkas yang tersedia | Status / catatan |
|-------|----------------------|------------------|
| Data siswa | [Smart Edu Hub - Data Siswa.png](<Screenshot/Smart Edu Hub - Data Siswa.png>), [Smart Edu Hub - Data Siswa (1).png](<Screenshot/Smart Edu Hub - Data Siswa (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Manajemen user | [Smart Edu Hub - Management User.png](<Screenshot/Smart Edu Hub - Management User.png>), [Smart Edu Hub - Management User (1).png](<Screenshot/Smart Edu Hub - Management User (1).png>) | ✅ Ada — dua ukuran tangkapan |
| Dashboard admin umum | — | ⬜ Belum ada bukti yang dapat diidentifikasi |

## 9. Bug Hunting

Dua berkas berikut ada di folder bukti. Nama berkasnya tidak mengaitkan gambar dengan ID bug tertentu, sehingga tidak dianggap sebagai bukti untuk temuan tertentu tanpa pemeriksaan/penamaan tambahan.

| Berkas | Status |
|--------|--------|
| [bug_hunt1.png](<Screenshot/bug_hunt1.png>) | ✅ Ada — bukti bug hunting, temuan belum dipetakan di dokumen ini |
| [bug_hunt2.png](<Screenshot/bug_hunt2.png>) | ✅ Ada — bukti bug hunting, temuan belum dipetakan di dokumen ini |

Screenshot bernama `bug01-*`, `bug02-*`, `bug03-*`, atau `bug06-*` yang sebelumnya direncanakan belum ditemukan di folder.

> Referensi detail: [`../../smart-acp/docs/bug-hunting.md`](../../smart-acp/docs/bug-hunting.md)



## 10. Screenshot Berukuran Mobile

Terdapat **18 berkas dengan lebar gambar 393 px**. Ukuran ini menunjukkan gambar berorientasi mobile, tetapi tidak dengan sendirinya memverifikasi viewport/perangkat atau interaksi tertentu. Bukti-bukti tersebut sudah dihitung dalam kategori pada ringkasan, bukan sebagai berkas tambahan.

Belum ada berkas bernama khusus untuk membuktikan TopBar admin, bottom navigation, atau bottom sheet. Untuk bukti tampilan tersebut, ambil screenshot yang menampilkan komponen terkait secara jelas dan beri nama deskriptif.

## 11. Ringkasan Status Bukti

| Kategori | Berkas tersedia | Catatan |
|----------|----------------:|---------|
| Pendaftaran siswa baru | 8 | Bukti hasil akhir diterima/ditolak dan halaman unduh belum ada |
| Portal siswa dan e-learning | 10 | Bukti ujian online belum ada |
| Absensi | 5 | Terminal, kartu, dan laporan tersedia; hasil scan berhasil belum teridentifikasi |
| Portal orang tua/wali | 5 | Login, dashboard, dan presensi anak tersedia |
| Modul keuangan | 8 | Dashboard, pengajuan, daftar kartu, dan rekap tersedia |
| Dashboard admin | 4 | Data siswa dan manajemen user tersedia; dashboard umum belum ada |
| Bug hunting | 2 | Berkas ada, tetapi belum dipetakan ke ID temuan |
| Bukti pengujian | 0 | Screenshot hasil test belum ada |
| **Total berkas di folder** | **42** | **40 screenshot fitur + 2 screenshot bug hunting** |

### Prioritas bukti yang masih perlu dilengkapi

1. Screenshot hasil pengujian aktual, tanpa mencantumkan angka sebelum test dijalankan.
2. Hasil akhir seleksi pendaftaran (diterima/ditolak), ujian online, dan dashboard admin umum.
3. Hasil scan absensi yang menunjukkan pencatatan berhasil.
4. Pemetaan `bug_hunt1.png` dan `bug_hunt2.png` ke temuan yang benar.
5. Screenshot khusus komponen navigasi mobile admin jika masih diperlukan.

## Struktur Folder

```text
docs/bukti/
├── README.md
└── Screenshot/
    ├── Attendance Terminal - Smart Axioo.png
    ├── Dashboard Siswa - Smart Edu Hub.png
    ├── Smart Edu Hub - Data Siswa.png
    ├── bug_hunt1.png
    ├── bug_hunt2.png
    └── ... berkas bukti lainnya
```

---

*© 2026 Tim Smart EduHub — Lomba Direktorat Jenderal Pendidikan Vokasi*
