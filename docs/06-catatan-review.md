# 06 — Catatan Review Kode

**Tanggal:** 2026-08-07  
**Tim:** Smart EduHUb

Dokumen ini berisi catatan hasil review kode selama pengembangan Smart EduHub.
 
---

## 1. Arsitektur & Struktur

### 1.1 Multi-Guard Autentikasi
- **Observasi:** Sistem menggunakan 3 guard terpisah: `web` (admin/guru), `student`, `parent`.
- **Komentar:** Pendekatan ini memisahkan sesi dengan baik, namun perlu perhatian pada konfigurasi `config/auth.php`.
- **Rekomendasi:** Pastikan setiap guard memiliki provider dan model yang benar.

### 1.2 Routing
- **Observasi:** Semua rute didefinisikan di satu file `routes/web.php` dengan grouping middleware.
- **Komentar:** Rute cukup terorganisir dengan prefix per peran (`siswa`, `wali`).
- **Rekomendasi:** Pertimbangkan memisahkan rute ke beberapa file jika semakin banyak.

---

## 2. Keamanan

### 2.1 Password & PIN
- **Observasi:** Password admin/guru di-hash dengan Bcrypt. Siswa menggunakan PIN (4 digit).
- **Komentar:** PIN 4 digit relatif lemah untuk keamanan finansial.
- **Rekomendasi:** Pertimbangkan menambah panjang PIN atau menambahkan faktor autentikasi lain untuk transaksi keuangan.

### 2.2 Validasi Input
- **Observasi:** Controller menggunakan Laravel validation rules.
- **Rekomendasi:** Pastikan semua input user divalidasi, terutama untuk transaksi keuangan dan upload file.

### 2.3 Otorisasi
- **Observasi:** Middleware `auth` melindungi rute admin/guru.
- **Rekomendasi:** Tambahkan policy/gate untuk kontrol akses yang lebih granular per fitur.

---

## 3. Database

### 3.1 Migrasi
- **Observasi:** Terdapat banyak file migrasi (+/- 100), beberapa berupa perubahan struktur (update, add column).
- **Komentar:** Migrasi incremental sudah baik untuk tracking perubahan.
- **Rekomendasi:** Pertimbangkan squashing migrasi untuk mengurangi jumlah file.

### 3.2 Relasi
- **Observasi:** Model menggunakan Eloquent relationships.
- **Rekomendasi:** Pastikan foreign key constraints konsisten dan ada index untuk kolom yang sering di-query.

---

## 4. Kode Controller

### 4.1 Ukuran Controller
- **Observasi:** Beberapa controller cukup besar (mis. `AdminFinanceController`, `CourseController`).
- **Rekomendasi:** Pertimbangkan memecah controller yang terlalu besar menjadi service class atau action class.

### 4.2 Duplikasi Kode
- **Observasi:** Pola CRUD serupa di beberapa controller.
- **Rekomendasi:** Gunakan trait atau base controller untuk logika yang berulang.

---

## 5. Frontend (Blade & Tailwind)

### 5.1 Struktur View
- **Observasi:** View dikelompokkan per peran (`admin/`, `siswa/`, `parent/`) dan per fitur.
- **Komentar:** Struktur cukup jelas dan mudah dinavigasi.

### 5.2 Konsistensi Styling
- **Observasi:** Menggunakan Tailwind CSS utility classes.
- **Rekomendasi:** Pertimbangkan component extraction untuk UI yang berulang (button, card, modal).

---

## 6. Testing

### 6.1 Cakupan Test
- **Observasi:** Baru ada `ExampleTest` (unit & feature).
- **Rekomendasi:** Tambahkan test untuk fitur inti (autentikasi, CRUD, transaksi keuangan).

### 6.2 Database Testing
- **Observasi:** Testing menggunakan SQLite in-memory.
- **Komentar:** Bagus untuk isolation dan kecepatan.

---

## 7. Dokumentasi

- **Observasi:** README sudah lengkap dengan 8 bagian.
- **Rekomendasi:** Tambahkan dokumentasi API jika ada endpoint yang dikonsumsi perangkat eksternal (scanner).

---

## 8. Daftar Perbaikan yang Disarankan

| Prioritas | Item | Status |
|-----------|------|--------|
| Tinggi | Tambahkan test untuk fitur inti | Belum |
| Tinggi | Perkuat keamanan PIN transaksi | Belum |
| Sedang | Refactor controller besar ke service | Belum |
| Sedang | Tambahkan policy/gate otorisasi | Belum |
| Rendah | Squashing migrasi | Belum |
| Rendah | Extract Blade components | Belum |
