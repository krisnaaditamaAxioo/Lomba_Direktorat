# Analisis Change Request

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

Dokumen ini mencatat dan menganalisis setiap perubahan (change request) yang terjadi selama pengembangan Smart EduHub. Perubahan disusun mengikuti alur di `03-log-prompt.md` (Gemini → Stitch → Laravel 12 → Trae per fase) dan kesalahan di `05-kesalahan-ai.md`.

---

## Daftar Change Request

| No. | Tanggal | Change Request | Status | Dampak |
|-----|---------|----------------|--------|--------|
| CR-01 | 2026-08-07 | Ganti istilah "PPDB" menjadi "Pendaftaran Anggota/Siswa Baru di Sistem" | ✅ Diselesaikan | Dokumentasi |
| CR-02 | 2026-08-07 | Ganti nama produk "Smart Axioo" menjadi "Smart EduHub" | ✅ Diselesaikan | Dokumentasi |
| CR-03 | 2026-08-07 | Rinci struktur folder `smart-acp` lebih detail | ✅ Diselesaikan | Dokumentasi |
| CR-04 | Fase 1–2 | Seragamkan tampilan Stitch menjadi layout Blade (sidebar, header) | ✅ Diselesaikan | Tampilan (View) |
| CR-05 | Fase 1–12 | Samakan nama route, view, dan controller | ✅ Diselesaikan | Kode |
| CR-06 | Fase 2–12 | Samakan nama kolom database dengan field di tampilan | ✅ Diselesaikan | Database, View |
| CR-07 | Fase 2–12 | Perbaiki urutan migrasi dan relasi antartabel | ✅ Diselesaikan | Database |
| CR-08 | Fase 1 | Sesuaikan konfigurasi middleware dengan Laravel 12 | ✅ Diselesaikan | Kode |

---

## CR-01: Perubahan Istilah PPDB → Pendaftaran Siswa Baru

### Latar Belakang
Awalnya fitur pendaftaran dilabeli sebagai "PPDB" (Penerimaan Peserta Didik Baru). Namun, konteks sebenarnya adalah pendaftaran anggota/siswa baru di dalam sistem, bukan PPDB formal. Fitur ini dibuat pada Fase 3.

### Alasan Perubahan
- Istilah "PPDB" memiliki konotasi proses penerimaan siswa baru secara formal yang melibatkan seleksi, tes, dll.
- Fitur ini lebih tepat disebut "Pendaftaran Anggota/Siswa Baru di Sistem" karena mencakup pendaftaran dan aktivasi akun di sistem.

### Ruang Lingkup Perubahan
- `README.md` — deskripsi, alur inti, URL, tangkapan layar, fitur, struktur folder
- `docs/bukti/README.md` — nama file tangkapan layar

### File yang Diubah
1. `README.md`
2. `docs/bukti/README.md`

### Verifikasi
- [x] Semua "PPDB" diganti menjadi "Pendaftaran Anggota/Siswa Baru di Sistem"
- [x] Nama file tangkapan layar diperbarui (`02-form-pendaftaran.png`, `03-cek-status-pendaftaran.png`)

---

## CR-02: Perubahan Nama Produk Aplikasi lomba → Smart EduHub

### Latar Belakang
Nama produk awalnya mengikuti `APP_NAME` di `.env` yaitu "Aplikasi lomba". Namun, nama yang benar untuk produk ini adalah "Smart EduHub".

### Alasan Perubahan
- Nama produk harus konsisten dengan identitas tim dan branding.
- "Smart EduHub" lebih merepresentasikan platform pendidikan (Education Hub).

### Ruang Lingkup Perubahan
- Semua penyebutan nama produk di dokumentasi

### File yang Diubah
1. `README.md`
2. `docs/bukti/README.md`

### Catatan
- Nama tim "Smart EduHUb" (dengan huruf H besar) tetap dipertahankan karena itu adalah nama tim, bukan nama produk.
- Nama produk "Smart EduHub" (huruf H kecil di "Hub") digunakan untuk konsistensi.

### Verifikasi
- [x] Semua "Aplikasi lomba" diganti menjadi "Smart EduHub"
- [x] Nama tim "Smart EduHUb" tetap utuh

---

## CR-03: Perincian Struktur Folder Smart EduHub

### Latar Belakang
Bagian struktur folder di README awalnya terlalu umum dan tidak merinci isi folder `Smart EduHub/`.

### Alasan Perubahan
- Dokumentasi struktur folder yang rinci membantu pengembang baru memahami organisasi kode.
- Memudahkan navigasi dan maintenance.

### Ruang Lingkup Perubahan
- Bagian "Struktur Folder" di `README.md`

### Detail Penambahan
- `app/` dirinci: Console/Commands, Exports, Http/Controllers (28+ controller dengan keterangan), Http/Middleware, Imports, Models (dikelompokkan per fitur), Observers, Providers
- `config/` — daftar file konfigurasi
- `database/` — migrations, seeders (per seeder dengan fungsi), factories
- `lang/` — id.json, en.json, ar.json
- `public/` — ab_acb/ (scanner), images/, storage/
- `resources/views/` — dirinci per peran (admin, siswa, parent) dan per fitur

### File yang Diubah
1. `README.md`

### Verifikasi
- [x] Struktur folder mencakup semua subfolder penting
- [x] Setiap file/folder memiliki keterangan singkat

---

## CR-04: Penyeragaman Tampilan Stitch menjadi Layout Blade

### Latar Belakang
Tampilan dibuat di Stitch dan disalin satu per satu ke `resources/views`. Karena setiap halaman dibuat terpisah, sidebar, header, dan gaya komponen berbeda antarhalaman.

### Alasan Perubahan
- Tampilan aplikasi harus seragam di semua peran (admin, siswa, orang tua).
- Bagian yang berulang sebaiknya ditulis sekali agar mudah dirawat.

### Ruang Lingkup Perubahan
- Folder `resources/views` (layout, sidebar, header)

### Verifikasi
- [x] Sidebar dan header dipakai ulang lewat layout Blade
- [x] Tampilan antarhalaman konsisten

---

## CR-05: Penyamaan Nama Route, View, dan Controller

### Latar Belakang
Trae membuat MVC sesuai tampilan di `resources/views`, tetapi nama route, file view, dan method controller tidak selalu sama, sehingga muncul error seperti `Route not defined` atau `View not found`.

### Alasan Perubahan
- Aplikasi harus berjalan tanpa error routing.
- Penamaan yang konsisten memudahkan pencarian kode.

### Ruang Lingkup Perubahan
- `routes/web.php`
- `app/Http/Controllers/`
- `resources/views/`

### Verifikasi
- [x] Semua menu dan tombol mengarah ke route yang benar
- [x] Tidak ada error route atau view saat aplikasi dijalankan

---

## CR-06: Penyamaan Nama Kolom Database dengan Field Tampilan

### Latar Belakang
Tampilan dari Stitch memakai nama field sendiri, sedangkan tabel MySQL dibuat oleh Trae dengan nama kolom yang berbeda, sehingga data tidak tersimpan atau tidak tampil.

### Alasan Perubahan
- Data di form, tabel, dan database harus saling terhubung.
- Fase berikutnya harus mengikuti nama kolom yang sudah ada.

### Ruang Lingkup Perubahan
- `database/migrations/`
- `app/Models/`
- `resources/views/` (form dan tabel)

### Verifikasi
- [x] Data dari form tersimpan di database
- [x] Data database tampil benar di halaman

---

## CR-07: Perbaikan Urutan Migrasi dan Relasi Antartabel

### Latar Belakang
Karena fitur dibuat bertahap per fase, beberapa tabel yang memakai foreign key dibuat sebelum tabel induknya (mis. tabel siswa sebelum angkatan dan tahun ajaran), sehingga `php artisan migrate` gagal.

### Alasan Perubahan
- Migrasi harus bisa dijalankan dari database kosong tanpa error.
- Relasi antartabel harus benar.

### Ruang Lingkup Perubahan
- `database/migrations/` (urutan nama file)
- `app/Models/` (relasi)

### Verifikasi
- [x] `php artisan migrate` berhasil dari database kosong
- [x] Relasi antarmodel berfungsi

---

## CR-08: Penyesuaian Konfigurasi Middleware dengan Laravel 12

### Latar Belakang
AI kadang memakai cara lama Laravel 9/10 (mendaftarkan middleware di `Kernel.php`) yang tidak dipakai lagi di Laravel 12. Hal ini terkait autentikasi multi-peran di Fase 1.

### Alasan Perubahan
- Proyek memakai Laravel 12, sehingga konfigurasi harus mengikuti strukturnya.
- Pembatasan akses per peran harus berjalan.

### Ruang Lingkup Perubahan
- `bootstrap/app.php`
- `app/Http/Middleware/`
- `routes/web.php`

### Verifikasi
- [x] Middleware peran terdaftar dengan benar
- [x] Halaman tiap peran hanya bisa dibuka oleh peran yang sesuai

---

## Ringkasan Dampak

- **CR-01 sampai CR-03** hanya berdampak pada **dokumentasi**.
- **CR-04 sampai CR-08** berdampak pada **kode aplikasi** (tampilan, route, controller, model, migrasi, dan middleware), tetapi tidak mengubah cakupan fitur Fase 1–13.

---

## Pelajaran

1. **Klarifikasi di Awal** — Sebelum membuat dokumentasi dan fitur, pastikan nama produk, istilah, dan nama kolom sudah disepakati.
2. **Konsistensi** — Gunakan `replace_all` untuk perubahan istilah dan samakan penamaan route, view, dan kolom.
3. **Verifikasi** — Selalu cek hasil perubahan dengan pencarian (grep) dan jalankan aplikasi.
4. **Uji Ulang per Fase** — Karena fitur dibuat bertahap, uji fase sebelumnya setelah menyelesaikan fase baru.