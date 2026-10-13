# 03 — Log Prompt AI

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

Dokumen ini mencatat interaksi prompt dengan AI selama pengembangan Smart EduHub.

---

## Daftar Prompt

### Prompt 1 — Membuat Prompt Tampilan di Gemini
**Input:**
```
Buatkan prompt untuk Stitch agar membuat tampilan (frontend) aplikasi Smart EduHub, sistem manajemen sekolah dengan fitur pendaftaran siswa baru, absensi, poin perilaku, e-learning, ujian online, keuangan, pengajuan izin, pengaduan, serta portal siswa dan orang tua.
```
**Output:** Prompt yang terstruktur dan detail untuk dipakai di Stitch.

---

### Prompt 2 — Membuat Tampilan di Stitch
**Input:**
```
(Prompt hasil dari Gemini disalin ke Stitch)
```
**Output:** Tampilan aplikasi Smart EduHub di Stitch. Kode frontend setiap halaman disalin satu per satu.

---

### Prompt 3 — Instalasi Laravel 12 & Penempatan Tampilan
**Input:**
```
(Langkah manual, tanpa prompt AI) Instal Laravel 12 di komputer lokal, lalu salin kode frontend dari Stitch satu per satu ke folder resources/views.
```
**Output:** Proyek Laravel 12 dengan seluruh tampilan frontend berada di `resources/views`.

---

### Prompt 4 — Fase 1: Setup Proyek & Autentikasi
**Input:**
```
Buatkan aplikasi sesuai dengan tampilan yang ada di resources/views. Buatkan sekaligus MVC dan database MySQL. Mulai dari setup proyek Laravel, koneksi database, dan autentikasi multi-peran.
```
**Output:** Konfigurasi proyek, database MySQL, dan autentikasi multi-peran.

---

### Prompt 5 — Fase 2: Master Data
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk master data: siswa, guru, angkatan, dan tahun ajaran.
```
**Output:** Model, Controller, dan migrasi master data.

---

### Prompt 6 — Fase 3: Pendaftaran Siswa Baru & Status
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk pendaftaran siswa baru beserta status pendaftarannya.
```
**Output:** Fitur pendaftaran siswa baru dan pengelolaan status.

---

### Prompt 7 — Fase 4: Absensi & Laporan
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk absensi (manual, bulk, scan QR) dan laporannya.
```
**Output:** Fitur absensi manual, bulk, scan QR, dan laporan.

---

### Prompt 8 — Fase 5: Poin Perilaku & Leaderboard
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk poin perilaku dan leaderboard.
```
**Output:** Fitur poin perilaku dan leaderboard.

---

### Prompt 9 — Fase 6: E-learning
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk e-learning: mata pelajaran, modul, soal, dan tugas.
```
**Output:** Fitur e-learning (mapel, modul, soal, tugas). 

---

### Prompt 10 — Fase 7: Ujian Online & Rekap Nilai
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk ujian online dan rekap nilai.
```
**Output:** Fitur ujian online dan rekap nilai.

---

### Prompt 11 — Fase 8: Keuangan / Simpan-Pinjam Digital
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk keuangan / simpan-pinjam digital.
```
**Output:** Fitur keuangan dan simpan-pinjam digital.

---

### Prompt 12 — Fase 9: Izin Siswa
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk izin siswa.
```
**Output:** Fitur izin siswa.

---

### Prompt 13 — Fase 10: Pengaduan & Notifikasi
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk pengaduan dan notifikasi.
```
**Output:** Fitur pengaduan dan notifikasi.

---

### Prompt 14 — Fase 11: Portal Siswa & Portal Orang Tua
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan MVC dan tabel MySQL untuk portal siswa dan portal orang tua.
```
**Output:** Portal siswa dan portal orang tua.

---

### Prompt 15 — Fase 12: Pengaturan Sistem, Multi-bahasa, Polishing
**Input:**
```
Lanjutkan sesuai tampilan di resources/views. Buatkan pengaturan sistem, fitur multi-bahasa, dan rapikan (polishing) seluruh aplikasi.
```
**Output:** Pengaturan sistem, multi-bahasa, dan perbaikan tampilan/fungsi.

---

### Prompt 16 — Fase 13: Testing, Dokumentasi, Finalisasi
**Input:**
```
Buatkan pengujian, dokumentasi, dan finalisasi aplikasi Smart EduHub.
```
**Output:** Pengujian dan dokumentasi aplikasi.

---

### Prompt 17 — Setup & Dokumentasi Awal
**Input:**
```
Nama dan deskripsi produk, apa yang dilakukan website ini dan untuk siapa, alur inti, langkah pengguna dari awal sampai akhir, cara menjalankan, prasyarat 
beserta versinya, perintah clone, instalasi, menjalankan, alamat browser, akun contoh, tangkapan layar alur inti, cara menjalankan pengujian, fitur dan batasan, struktur folder. Tambahkan nama tim Smart EduHUb, ketua Krisna Aditama, anggota Najih Banyu Wicaksono & Mujahid Muhammad Abul Ghofur, pembimbing Jeri Wilaga. Buatkan readme.md.
```
**Output:** README.md dengan 8 bagian lengkap.

---

### Prompt 18 — Koreksi Istilah PPDB
**Input:**
```
itu bukan ppdb tetapi untuk pendaftaran anggota/siswa baru di sistem
```
**Output:** Mengganti semua istilah "PPDB" menjadi "Pendaftaran Anggota/Siswa Baru di Sistem" di README.md dan docs/bukti/README.md.

---

### Prompt 19 — Ganti Nama Produk
**Input:**
```
smart axioo di ganti dengan smart eduhub
```
**Output:** Mengganti semua "Smart Axioo" menjadi "Smart EduHub" (nama produk), nama tim "Smart EduHUb" tetap utuh.

---

### Prompt 20 — Rincian Struktur Folder
**Input:**
```
di struktur folder aplikasi smart acp di buat lebih rinci lagi
```
**Output:** Memperbarui bagian Struktur Folder dengan rincian lengkap per subfolder (Controllers, Models, Views per peran, dll.).

---

### Prompt 21 — Buat Dokumentasi Pendukung
**Input:**
```
01-spesifikasi.md, 02-rencana.md, 03-log-prompt.md, 04-kasus-uji.md, 05-kesalahan-ai.md, 06-catatan-review.md, 07-refleksi.md, 08-penjelasan-kode.md, analisis-change-request.md. Buatkan di dalam folder docs
```
**Output:** Membuat 9 file dokumentasi di folder docs/.

---

## Catatan Penggunaan AI

1. AI digunakan untuk **mempercepat** pembuatan tampilan, kode boilerplate, MVC, dokumentasi, dan struktur file.
2. Alur kerja: Gemini (membuat prompt) → Stitch (membuat tampilan) → Laravel 12 (`resources/views`) → Trae (membuat MVC dan database MySQL) secara bertahap per fitur.
3. Semua output AI **direview dan diverifikasi** oleh tim sebelum diimplementasikan.
4. AI tidak menggantikan peran developer dalam pengambilan keputusan arsitektur.
5. Prompt yang efektif harus menyertakan konteks yang jelas dan ekspektasi output yang spesifik.
6. Dokumentasikan setiap change request dari AI ke `analisis-change-request.md`.