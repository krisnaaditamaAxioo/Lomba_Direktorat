# 02 — Rencana Pengembangan Smart EduHub

**Tanggal:** 2026-08-07 
**Tim:** Smart EduHUb

---

## 1. Metodologi

Pengembangan menggunakan pendekatan **iteratif-incremental** dengan pembagian fase sesuai modul fitur. Setiap fase menghasilkan fitur yang dapat diuji dan direview sebelum lanjut ke fase berikutnya.

---

## 2. Timeline & Fase Pengembangan

| Fase | Modul | Durasi | Status |
|---------|-------------|---------|---------|   
| Fase 1 | Setup proyek Laravel, database, autentikasi multi-peran	| 4 hari |	✅ Selesai |
| Fase 2 | Master data (siswa, guru, angkatan, tahun ajaran)	| 5 hari |	✅ Selesai |
| Fase 3 | Pendaftaran siswa baru & status	| 5 hari |	✅ Selesai |
| Fase 4 | Absensis (manual, bulk, scan QR) & laporan	| 5 hari |	✅ Selesai |
| Fase 5 | Poin perilaku & leaderboard	| 4 hari |	✅ Selesai |
| Fase 6 | E-learning (mapel, modul, soal, tugas)	| 7 hari |	✅ Selesai |
| Fase 7 | Ujian online & rekap nilai	| 6 hari |	✅ Selesai |
| Fase 8 | Keuangan / simpan-pinjam digital	| 6 hari |	✅ Selesai |
| Fase 9 | Izin siswa	| 5 hari |	✅ Selesai |
| Fase 10 | Pengaduan & notifikasi	| 4 hari |	✅ Selesai |
| Fase 11 | Portal siswa & portal orang tua	| 5 hari |	✅ Selesai |
| Fase 12 | Pengaturan sistem, multi-bahasa, polishing	| 5 hari |	✅ Selesai |
| Fase 13 | Testing, dokumentasi, finalisasi	| 6 hari |	🔄 Berjalan |           

**Total estimasi:** ±10 minggu

---

## 3. Pembagian Tugas Tim

| Anggota | Fokus Modul |
|---------|-------------|
| **Krisna Aditama** (Ketua) | Arsitektur sistem, autentikasi, keuangan, integrasi |
| **Najih Banyu Wicaksono** | Pendaftaran, absensi, ujian, poin |
| **Mujahid Muhammad Abul Ghofur** | E-learning, izin, portal siswa/orang tua |

> Kolaborasi dilakukan melalui version control (Git) dan review code bersama pembimbing.

---

## 4. Teknologi yang Dipakai

### Backend
- **Laravel 12** — framework PHP
- **PHP ^8.2**
- **MySQL 8.0** — database utama
- **SQLite** — database testing
- **DomPDF** — generate PDF
- **Maatwebsite Excel** — impor/ekspor Excel
- **SimpleSoftwareIO QR Code** — generate QR code

### Frontend
- **Tailwind CSS v4** — styling
- **Vite 7** — build tool
- **Blade** — templating engine
- **Font Awesome** — ikon

### Testing
- **PHPUnit 11** — unit & feature testing

---

## 5. Rencana Testing

1. **Unit Testing** — test model dan logika bisnis inti.
2. **Feature Testing** — test endpoint HTTP dan alur pengguna.
3. **Manual Testing** — uji setiap fitur sesuai kasus uji (lihat `04-kasus-uji.md`).
4. **Cross-browser Testing** — Chrome, Edge, Firefox.
5. **Responsive Testing** — desktop, tablet, mobile.

---

## 6. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Keterlambatan pengembangan modul | Prioritaskan fitur inti, fitur tambahan bisa ditunda |
| Bug pada integrasi antar modul | Lakukan integrasi bertahap & testing menyeluruh |
| Perubahan requirement di tengah jalan | Dokumentasikan change request (lihat `analisis-change-request.md`) |
| Keamanan data | Gunakan hashing, validasi input, middleware auth |

---

## 7. Deliverable Akhir

1. Aplikasi web Smart EduHub yang dapat dijalankan.
2. Database terstruktur dengan data contoh.
3. Dokumentasi lengkap (README + folder docs/).
4. Tangkapan layar alur inti (folder docs/bukti/).
5. Laporan pengembangan (file-file dalam docs/).
