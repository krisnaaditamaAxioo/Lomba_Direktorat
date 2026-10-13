# 05 — Catatan Kesalahan AI

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

Dokumen ini mencatat kesalahan yang dihasilkan AI selama pengembangan Smart EduHub dan bagaimana tim menanganinya. Kesalahan disusun berdasarkan alur prompt di `03-log-prompt.md`: Gemini → Stitch → Laravel 12 → Trae (per fase).

---

## A. Kesalahan pada Tahap Prompt & Tampilan (Gemini dan Stitch)

### 1. Prompt dari Gemini Terlalu Umum atau Tidak Lengkap

**Konteks:** Prompt untuk Stitch dibuat oleh Gemini (Prompt 1).

**Kesalahan:** Prompt hasil Gemini sering melewatkan halaman atau peran tertentu (mis. portal orang tua, halaman scan QR), sehingga tampilan dari Stitch tidak lengkap.

**Penanganan:** Prompt dari Gemini dicek ulang terhadap daftar fitur Fase 1–13 sebelum disalin ke Stitch. Halaman yang kurang dibuat lewat prompt tambahan.

---

### 2. Tampilan Stitch Tidak Konsisten Antarhalaman

**Konteks:** Tampilan dibuat dan disalin satu per satu dari Stitch (Prompt 2).

**Kesalahan:** Warna, gaya tombol, sidebar, dan nama menu berbeda-beda antarhalaman karena setiap halaman dibuat terpisah.

**Penanganan:** Kode frontend dirapikan setelah disalin. Bagian yang berulang (sidebar, header) dipisah menjadi layout Blade agar seragam.

---

### 3. Kode Frontend Berupa HTML Statis dan Data Dummy

**Konteks:** Kode dari Stitch ditempatkan di `resources/views` (Prompt 3).

**Kesalahan:** Tampilan masih berisi data contoh yang ditulis langsung di HTML, tautan `href="#"`, dan belum memakai sintaks Blade (`@extends`, `@section`, `{{ }}`).

**Penanganan:** Pada tiap fase, Trae diminta mengganti data dummy dengan data dari database dan mengubah tautan menjadi route Laravel.

---

## B. Kesalahan pada Tahap Pembuatan Aplikasi (Trae, Fase 1–13)

### 4. Nama Route, View, dan Controller Tidak Cocok

**Konteks:** Trae membuat MVC sesuai tampilan di `resources/views` (Prompt 4–16).

**Kesalahan:** Nama route, nama file view, dan nama method controller tidak sama sehingga muncul error seperti `Route not defined` atau `View not found`.

**Penanganan:** Tim memeriksa `routes/web.php` dan struktur `resources/views`, lalu meminta Trae menyesuaikan nama agar konsisten.

---

### 5. Urutan Migrasi dan Relasi Tabel Salah

**Konteks:** Pembuatan tabel MySQL dilakukan bertahap per fase.

**Kesalahan:** Tabel yang memakai foreign key dibuat sebelum tabel induknya (mis. tabel siswa sebelum angkatan/tahun ajaran), sehingga `php artisan migrate` gagal.

**Penanganan:** Urutan migrasi diperbaiki (mengganti timestamp nama file migrasi) dan relasi di Model dicek ulang.

---

### 6. Nama Kolom di View Tidak Sama dengan Database

**Konteks:** View dari Stitch memakai istilah sendiri, sedangkan tabel dibuat oleh Trae.

**Kesalahan:** Field di form dan tabel (mis. `nama_lengkap`) tidak sama dengan kolom di database (mis. `name`), sehingga data tidak tersimpan atau tidak tampil.

**Penanganan:** Nama kolom dan field disamakan, lalu fase berikutnya diberi instruksi agar mengikuti kolom yang sudah ada.

---

### 7. Sintaks Laravel Versi Lama

**Konteks:** Proyek memakai Laravel 12.

**Kesalahan:** AI kadang memakai cara lama Laravel 9/10, seperti mendaftarkan middleware di `Kernel.php` yang tidak dipakai lagi di Laravel 12.

**Penanganan:** Prompt menyebutkan versi Laravel 12 secara eksplisit. Middleware dan konfigurasi dipindahkan ke `bootstrap/app.php`.

---

### 8. Autentikasi Multi-Peran Tidak Membatasi Akses

**Konteks:** Fase 1 membuat autentikasi multi-peran.

**Kesalahan:** Halaman peran tertentu (mis. admin) masih bisa dibuka lewat URL oleh peran lain karena middleware peran belum dipasang di semua route.

**Penanganan:** Route dikelompokkan per peran dengan middleware, lalu diuji dengan login memakai tiap peran.

---

### 9. Fitur Tambahan yang Tidak Diminta (Over-Generasi)

**Konteks:** Setiap fase diminta dibuat sekaligus (MVC dan database).

**Kesalahan:** AI menambahkan fitur, tabel, atau halaman yang tidak ada di tampilan maupun daftar fase.

**Penanganan:** Prompt dibatasi pada satu fase dan diberi instruksi "hanya sesuai tampilan yang ada". Kode berlebih dihapus saat review.

---

### 10. Validasi dan Keamanan Form Kurang

**Konteks:** Pembuatan form pendaftaran, absensi, keuangan, ujian, dan lainnya.

**Kesalahan:** Form belum memakai validasi input, kadang tanpa `@csrf`, dan hak akses pada data belum dicek (mis. siswa melihat data siswa lain).

**Penanganan:** Validasi, `@csrf`, dan pengecekan kepemilikan data ditambahkan dan diuji manual.

---

### 11. Fitur Spesifik Tidak Berjalan Sempurna

**Konteks:** Fase dengan logika khusus (scan QR di Fase 4, ujian online di Fase 7, simpan-pinjam di Fase 8).

**Kesalahan:** Contohnya scan QR tidak membaca kode dengan benar, timer ujian tidak sinkron, atau perhitungan saldo/bunga salah.

**Penanganan:** Fitur diuji dengan data nyata, hasil perhitungan dicek manual, lalu diperbaiki lewat prompt koreksi.

---

### 12. Perubahan Fase Baru Merusak Fase Sebelumnya

**Konteks:** Pembuatan dilakukan bertahap sehingga file yang sama bisa diubah di banyak fase.

**Kesalahan:** Fase baru menimpa atau mengubah kode fase sebelumnya (mis. layout, route, atau Model), sehingga fitur lama error.

**Penanganan:** Setelah tiap fase, fitur fase sebelumnya diuji ulang sebelum lanjut. Perubahan dicatat di `analisis-change-request.md`.

---

## C. Kesalahan pada Dokumentasi

### 13. Kesalahan Penamaan Produk

**Konteks:** Saat awal pembuatan README, AI menggunakan nama "Aplikasi lomba" karena membaca dari `APP_NAME` di `.env`.

**Kesalahan:** Nama produk seharusnya "Smart EduHub" (nama tim juga "Smart EduHUb"), bukan "Aplikasi lomba".

**Penanganan:** User memberikan feedback, AI mengganti semua "Aplikasi lomba" menjadi "Smart EduHub" di seluruh dokumentasi. Nama tim "Smart EduHUb" dipertahankan.

---

### 14. Kesalahan Istilah PPDB

**Konteks:** AI mengasumsikan fitur pendaftaran adalah PPDB (Penerimaan Peserta Didik Baru).

**Kesalahan:** Fitur tersebut sebenarnya adalah pendaftaran anggota/siswa baru di sistem, bukan PPDB formal.

**Penanganan:** User mengoreksi, AI mengganti semua istilah "PPDB" menjadi "Pendaftaran Anggota/Siswa Baru di Sistem" di README.md dan docs/bukti/README.md.

---

### 15. Kesalahan Penamaan Folder Scanner

**Konteks:** AI menyebut folder `public/ab_acp/` sebagai endpoint scanner.

**Kesalahan:** Nama folder yang benar adalah `public/ab_acb/`, bukan `ab_acp`.

**Penanganan:** Verifikasi ulang struktur folder dan koreksi nama menjadi `ab_acb`.

---

## D. Risiko Umum Penggunaan AI

### 16. AI Membuat Asumsi Tanpa Verifikasi

**Pola:** AI cenderung membuat asumsi tentang struktur folder, nama file, atau fungsi tanpa membaca file terlebih dahulu.

**Mitigasi:** Selalu perintahkan AI untuk **membaca file terlebih dahulu** sebelum membuat perubahan.

---

### 17. Konsistensi Penamaan

**Pola:** AI dapat menggunakan variasi nama yang berbeda untuk konsep yang sama (mis. "siswa" vs "student", "orang tua" vs "wali").

**Mitigasi:** Tetapkan konvensi penamaan di awal dan gunakan pada semua prompt.

---

## E. Pelajaran yang Dipetik

1. **Kerjakan bertahap per fitur** agar kesalahan mudah ditemukan sebelum menumpuk.
2. **Selalu verifikasi** output AI dengan menjalankan aplikasi dan membaca file asli.
3. **Berikan feedback eksplisit** saat AI salah, jangan biarkan kesalahan berlanjut ke fase berikutnya.
4. **Sebutkan versi dan batasan** (Laravel 12, MySQL, sesuai tampilan) di dalam prompt.
5. **Uji ulang fase sebelumnya** setiap selesai fase baru.
6. **Review manual** tetap diperlukan untuk kode yang dihasilkan AI.
7. **Dokumentasikan setiap koreksi** untuk referensi di masa depan.