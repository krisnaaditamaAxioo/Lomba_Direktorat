# 04 — Kasus Uji Smart EduHub

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

---

## 1. Kasus Uji Autentikasi

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| AU-01 | Login admin berhasil | Buka `/login`, masukkan `admin@smartaxioo.com` & `password`, klik login | Redirect ke `/dashboard` admin | Telah di uji |
| AU-02 | Login siswa berhasil | Buka `/siswa/login`, masukkan `siswa123` & PIN `1234` | Redirect ke `/siswa/dashboard` | Telah di uji |
| AU-03 | Login orang tua berhasil | Buka `/wali/login`, masukkan kredensial | Redirect ke `/wali/dashboard` | Telah di uji |
| AU-04 | Login dengan kredensial salah | Masukkan email/password salah | Tampil pesan error | Telah di uji |
| AU-05 | Logout | Klik tombol logout | Redirect ke halaman login | Telah di uji |

---

## 2. Kasus Uji Pendaftaran Siswa Baru

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| PS-01 | Tampilkan form pendaftaran | Buka `/daftar` | Form pendaftaran tampil | Telah di uji |
| PS-02 | Submit form valid | Isi semua field wajib, klik kirim | Data tersimpan, dapat ID pendaftaran | Telah di uji |
| PS-03 | Submit form tidak valid | Kosongkan field wajib | Tampil pesan validasi | Telah di uji |
| PS-04 | Cek status pendaftaran | Buka `/cek-status`, masukkan ID | Status pendaftaran tampil | Telah di uji |
| PS-05 | Admin ubah status pendaftaran | Login admin, buka `/registrations`, ubah status | Status berubah (change request) | Telah di uji |
| PS-06 | Download info hasil seleksi | Buka `/download-info` | File PDF terunduh | Telah di uji |

---

## 3. Kasus Uji Absensi

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| AB-01 | Input absensi manual | Pilih siswa, tanggal, status, simpan | Data absensi tersimpan | Telah di uji |
| AB-02 | Input absensi bulk | Upload file / pilih banyak siswa | Banyak data tersimpan | Telah di uji |
| AB-03 | Scan QR absensi | Buka `/attendance-scan`, scan kartu QR | Kehadiran tercatat | Telah di uji |
| AB-04 | Lihat laporan absensi | Buka `/attendance/report` | Laporan tampil | Telah di uji |

---

## 4. Kasus Uji E-Learning & Ujian

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| EL-01 | Buat mata pelajaran | Login admin, buka `/courses`, buat mapel | Mapel tersimpan | Telah di uji |
| EL-02 | Buat modul dalam mapel | Buka mapel, buat modul | Modul tersimpan | Telah di uji |
| EL-03 | Siswa baca modul | Login siswa, buka modul | Halaman modul tampil | Telah di uji |
| EL-04 | Siswa kerjakan soal modul | Buka soal, jawab, kirim | Jawaban tersimpan | Telah di uji |
| EL-05 | Siswa kumpul tugas | Upload file tugas, kirim | Tugas terkumpul | Telah di uji |
| EL-06 | Guru nilai tugas | Buka submission, beri nilai | Nilai tersimpan | Telah di uji |
| UJ-01 | Admin buat bank soal | Buka `/management/exams`, buat soal | Soal tersimpan | Telah di uji |
| UJ-02 | Siswa ikut ujian | Buka ujian, jawab, submit | Hasil tersimpan | Telah di uji |
| UJ-03 | Lihat rekap nilai | Buka hasil ujian | Rekap tampil | Telah di uji |

---

## 5. Kasus Uji Keuangan / Simpan Pinjam Digital

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| KU-01 | Siswa request kartu | Buka `/siswa/finance/request-card`, kirim | Request tersimpan | Telah di uji |
| KU-02 | Admin/Teller/BUMS approve request kartu | Login sebagai Teller/BUMS, buka `/finance/requests`, approve | Kartu aktif | Telah di uji |
| KU-03 | Admin/Teller/BUMS top-up kartu | Buka detail kartu, top-up nominal | Saldo bertambah | Telah di uji |
| KU-04 | Siswa transfer ke siswa lain | Buka transfer, masukkan NIS & PIN | Saldo berkurang/bertambah | Telah di uji |
| KU-05 | Admin/Teller/BUMS tarik tunai | Buka detail kartu, withdraw | Saldo berkurang | Telah di uji |
| KU-06 | Lihat riwayat transaksi | Buka riwayat | Daftar transaksi tampil | Telah di uji |
| KU-07 | Ekspor rekap keuangan | Klik ekspor Excel/PDF | File terunduh | Telah di uji |
| KU-08 | Teller login & landing ke dashboard keuangan | Login sebagai Teller di `/login` | Redirect ke `admin.finance.dashboard` | Telah di uji |
| KU-09 | BUMS login & landing ke riwayat pembayaran | Login sebagai BUMS di `/login` | Redirect ke `admin.finance.payments.history` | Telah di uji |
| KU-10 | Teller/BUMS tidak bisa akses menu non-keuangan | Login sebagai Teller/BUMS, coba akses `/students` | Ditolak / tidak ada menu | Telah di uji |
| KU-11 | Admin buat akun Teller | Menu Management Users, pilih role `teller` | Akun Teller tersimpan | Telah di uji |
| KU-12 | Admin buat akun BUMS | Menu Management Users, pilih role `bums` | Akun BUMS tersimpan | Telah di uji |

---

## 6. Kasus Uji Poin & Leaderboard

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| PO-01 | Beri poin ke siswa | Pilih siswa, isi tipe & skor, simpan | Poin tersimpan | Telah di uji |
| PO-02 | Lihat leaderboard | Buka `/points/leaderboard` | Ranking poin tampil | Telah di uji |
| PO-03 | Atur aturan poin | Buka `/point-rules`, buat aturan | Aturan tersimpan | Telah di uji |

---

## 7. Kasus Uji Izin Siswa

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| IZ-01 | Siswa ajukan izin | Buka `/siswa/permit`, isi, kirim | Izin tersimpan | Telah di uji |
| IZ-02 | Admin approve izin | Buka `/student-permits`, ubah status | Status berubah | Telah di uji |

---

## 8. Kasus Uji Pengaduan & Notifikasi

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| PG-01 | Siswa kirim pengaduan | Buka `/siswa/complaints/create`, isi, kirim | Pengaduan tersimpan | Telah di uji |
| PG-02 | Admin tanggapi pengaduan | Buka `/complaints`, tanggapi | Tanggapan tersimpan | Telah di uji |
| NT-01 | Admin kirim notifikasi | Buka `/notifications`, buat, kirim | Notifikasi terkirim ke siswa | Telah di uji |
| NT-02 | Siswa lihat notifikasi | Buka `/siswa/notifications` | Daftar notifikasi tampil | Telah di uji |

---

## 9. Kasus Uji Portal Orang Tua/Wali

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| OT-01 | Orang tua register | Buka `/wali/register`, isi, daftar | Akun terbuat | Telah di uji |
| OT-02 | Orang tua lihat dashboard anak | Login, buka dashboard | Data anak tampil | Telah di uji |
| OT-03 | Orang tua lihat absensi anak | Buka `/wali/attendance` | Absensi anak tampil | Telah di uji |
| OT-04 | Orang tua lihat keuangan anak | Buka `/wali/finance` | Saldo & transaksi tampil | Telah di uji |

---

## 10. Kasus Uji Pengaturan Sistem

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| ST-01 | Ubah tahun ajaran aktif | Buka `/settings`, set tahun ajaran | Tahun ajaran aktif berubah | Telah di uji |
| ST-02 | Backup data | Klik backup | File backup terunduh | Telah di uji |
| ST-03 | Reset data | Klik reset | Data tereset | Telah di uji |
| ST-04 | Tambah hari libur | Buka `/holidays`, tambah | Hari libur tersimpan | Telah di uji |

---

## 11. Kasus Uji Non-Fungsional

| ID | Kasus Uji | Langkah | Hasil Diharapkan | Status |
|----|-----------|---------|------------------|--------|
| NF-01 | Responsif mobile | Buka di resolusi mobile | Tampilan responsif | Telah di uji |
| NF-02 | Cross-browser | Buka di Chrome, Edge, Firefox | Konsisten | Telah di uji |
| NF-03 | Halaman utama 200 OK | Jalankan test `php artisan test` | Test passed | Telah di uji |
