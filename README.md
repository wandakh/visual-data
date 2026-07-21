# Visual Data — Sales Monitoring & Multi-Branch Analytics System

Sistem monitoring penjualan internal berbasis web, dibangun untuk mengelola dan menganalisis ratusan ribu baris data transaksi lintas cabang, dengan kontrol akses berbasis peran dan cabang (ORG_CODE), audit trail penuh, dan dashboard analitik yang berbeda untuk setiap level pengguna.

Proyek ini awalnya dikembangkan sebagai proyek Praktik Kerja Lapangan (PKL) untuk **Kisel** (anak perusahaan Telkomsel) dan meraih predikat **PKL Terbaik**. Versi ini adalah hasil rombak total (rebuild) dari basis kode Laravel 8 yang lama menjadi arsitektur Laravel 12 modern, dengan puluhan perbaikan bug, penambahan fitur enterprise-grade, dan modernisasi tampilan penuh.

---

## Daftar Isi

- [Ringkasan](#ringkasan)
- [Fitur Utama](#fitur-utama)
- [Tumpukan Teknologi](#tumpukan-teknologi)
- [Sorotan Arsitektur & Teknis](#sorotan-arsitektur--teknis)
- [Matriks Peran & Hak Akses](#matriks-peran--hak-akses)
- [Struktur Proyek](#struktur-proyek)
- [Skema Database (Ringkas)](#skema-database-ringkas)
- [Instalasi & Menjalankan Proyek](#instalasi--menjalankan-proyek)
- [Keamanan](#keamanan)
- [Keterbatasan & Rencana Pengembangan](#keterbatasan--rencana-pengembangan)
- [Latar Belakang Proyek](#latar-belakang-proyek)

---

## Ringkasan

Visual Data mengelola data transaksi penjualan (customer, produk, harga, margin, pajak) untuk sebuah bisnis distribusi multi-cabang. Sistem ini menangani:

- **Skala data nyata** — diuji langsung dengan file impor Excel berisi **211.000+ baris** transaksi (29MB), bukan data dummy.
- **Multi-cabang** — setiap cabang (`ORG_CODE`) punya datanya sendiri; karyawan cabang otomatis terkunci hanya ke data cabang mereka, sementara admin punya visibilitas global.
- **Audit penuh** — setiap perubahan data, login, export, dan aktivitas sensitif lainnya tercatat dengan pemisahan log yang jelas (perubahan data vs aktivitas akun).
- **Kontrol waktu akses** — jam kerja otomatis dibatasi untuk karyawan (07.00–18.00), dengan mekanisme pengajuan akses lembur mandiri yang tetap tercatat untuk akuntabilitas.

---

## Fitur Utama

### Dashboard & Data Penjualan
- Tabel data dengan pagination, pencarian bebas (nama customer / kode produk), filter tanggal, dan filter customer
- Kartu statistik real-time (Total Transaksi, Total Pendapatan, Total Customer) yang mengikuti filter aktif
- Form tambah/edit data dengan **dropdown yang menyesuaikan otomatis** — begitu customer dipilih, pilihan kolom lain (kode produk, tipe mitra, dst.) langsung dipersempit ke pola yang biasa dipakai customer tersebut
- Panel detail per transaksi yang dikelompokkan (Info Umum, Produk & Mitra, Pajak, Finansial)

### Soft Delete & Retensi Data
- Data yang dihapus **tidak langsung hilang** — masuk ke "Data Terhapus" dan bisa dipulihkan dalam 24 jam
- Setelah 24 jam, data terhapus otomatis dibersihkan permanen
- Setiap penghapusan **wajib disertai alasan** (Salah ketik, Data duplikat, Data tidak valid, Lainnya) lewat modal konfirmasi 2 langkah — mencegah penghapusan tidak sengaja
- Widget rekap jumlah penghapusan per kategori alasan

### Import & Export Excel
- Import mendukung file besar (hingga 100MB / ratusan ribu baris) lewat **chunked reading + batch insert**, mencegah timeout dan kehabisan memori
- Pembacaan kolom berbasis **nama header** (bukan posisi), dengan beberapa alias per kolom — tahan terhadap variasi urutan/format file sumber
- Laporan hasil import yang jelas: jumlah baris berhasil, dilewati, dan ditolak (termasuk penolakan otomatis untuk baris yang `ORG_CODE`-nya bukan milik cabang karyawan yang mengimpor)
- Export "Semua Kolom" vs "Summary" (rekap agregat per customer) dengan struktur kolom yang benar-benar berbeda
- Export otomatis mengikuti filter yang sedang aktif di layar (tanggal, pencarian, dsb.)

### Analitik & Diagram (Berbeda per Peran)
| Admin (Helicopter View) | Karyawan (Fokus Cabang) |
|---|---|
| Tren Pendapatan vs Keuntungan (line chart 2 seri) | Tren Transaksi Harian (jumlah transaksi saja) |
| Top Performa Cabang (bar chart) | — |
| Top Customer **Global** lintas cabang | Top Customer **Cabang sendiri** |
| Komposisi Penjualan per Tipe Produk (donut) | Produk Terlaris Cabang (donut) |

Data margin/keuntungan **tidak pernah dihitung atau dikirim** ke tampilan Karyawan — bukan cuma disembunyikan di CSS, tapi memang tidak ada di response server.

### Manajemen User & Multi-Cabang
- Tidak ada pendaftaran akun publik — akun hanya dibuat oleh Admin lewat halaman "Kelola User"
- Setiap akun Karyawan wajib terikat ke satu `ORG_CODE` (cabang)
- Akun bisa **dinonaktifkan** (soft delete) saat karyawan resign — langsung tidak bisa login, tapi riwayat log tetap utuh dan tetap menampilkan nama mereka
- Semua query data (dashboard, diagram, dropdown, import, export) otomatis terfilter sesuai `ORG_CODE` milik user yang sedang login — dipaksakan di level server, bukan cuma disembunyikan di tampilan

### Jam Akses & Lembur
- Karyawan hanya bisa mengakses sistem pukul 07.00–18.00; Admin akses 24 jam
- Di luar jam kerja, karyawan diarahkan ke halaman pengajuan akses lembur (alasan + durasi tambahan), self-service namun tetap tercatat penuh untuk audit
- Notifikasi peringatan otomatis 10 menit sebelum sesi berakhir
- Draft form yang sedang diisi otomatis disimpan ke browser sebelum auto-logout paksa, dan bisa dipulihkan saat login kembali

### Audit Log (Dipisah per Kategori)
- **Log Data** — riwayat tambah/edit/hapus/pulihkan data, import, dan export (masing-masing kategori kartu terpisah, dengan detail seperti rentang tanggal data yang diimpor)
- **Log Login** — riwayat login/logout per role (Admin & Karyawan dipisah tabelnya), percobaan login gagal, dan pengajuan lembur
- Rekap harian (jumlah login unik per role, bukan jumlah aktivitas) dan indikator user yang masih aktif di luar jam kerja

### Antarmuka
- Desain modern berbasis Tailwind CSS v4 + Alpine.js (menggantikan AdminLTE/Bootstrap/jQuery lama)
- Palet warna & tipografi konsisten (Manrope untuk judul, Inter untuk data), ikon SVG custom di seluruh aplikasi
- Toast notification, loading state pada semua aksi submit, progress bar navigasi, empty state yang informatif, dan pagination custom

---

## Tumpukan Teknologi

| Kategori | Teknologi |
|---|---|
| Backend | PHP 8.3+, Laravel 12 |
| Database | MySQL / MariaDB |
| Frontend | Tailwind CSS v4, Alpine.js, Vite |
| Otorisasi | Spatie Laravel-Permission (role & permission) |
| Excel | Maatwebsite/Laravel-Excel (PhpSpreadsheet) — chunked reading, batch insert |
| Visualisasi | Chart.js 4 |
| Font | Manrope, Inter (Google Fonts) |

---

## Sorotan Arsitektur & Teknis

Beberapa keputusan teknis yang jadi nilai tambah proyek ini:

- **Skalabilitas import data nyata**: import 211.000+ baris (29MB) awalnya gagal karena batas ukuran file dan pendekatan insert satu-per-satu. Diselesaikan dengan `WithChunkReading` + `WithBatchInserts` dari Laravel Excel, plus penyesuaian konfigurasi PHP (`upload_max_filesize`, `memory_limit`, `max_execution_time`).
- **Data scoping yang konsisten & aman**: alih-alih menambal filter `ORG_CODE` di tiap controller secara terpisah, dibuat method `SalesRecord::applyFilters()` dan `User::scopedOrgCode()` yang dipakai bersama oleh Dashboard, Diagram, Import, dan Export — memastikan tidak ada celah kebocoran data antar cabang.
- **Migration yang aman untuk data produksi**: keputusan sadar untuk **tidak** mengubah tipe data kolom lama (tetap `string`, bukan `decimal`/`date`) demi kompatibilitas dengan data existing, dengan catatan eksplisit soal trade-off ini didokumentasikan di kode.
- **Migration idempotent**: migration penghapus kolom lama (`role`, `role_id`) ditulis defensif dengan `Schema::hasColumn()`, sehingga aman dijalankan baik di database yang sempat punya kolom itu maupun yang tidak pernah punya — instalasi baru dan instalasi lama sama-sama konsisten.
- **Integritas audit log lintas soft-delete**: relasi `user()` di model log menggunakan `withTrashed()`, sehingga riwayat aktivitas tetap menampilkan nama user yang sudah dinonaktifkan, bukan `null`.
- **Retensi data otomatis tanpa scheduler eksternal**: pembersihan data yang lewat masa retensi 24 jam dilakukan secara *lazy purge* (saat halaman Data Terhapus diakses) — cukup untuk kebutuhan operasional sehari-hari tanpa perlu setup cron job terpisah.

---

## Matriks Peran & Hak Akses

| Aksi | Admin | Karyawan |
|---|:---:|:---:|
| Lihat data & diagram | Semua cabang | Cabang sendiri saja |
| Lihat kolom HPP & Margin | ✅ | ❌ |
| Tambah / Edit data | ✅ | ✅ (terkunci ke `ORG_CODE` sendiri) |
| Hapus data / kelola Trash | ✅ | ❌ |
| Import Excel | ✅ (semua baris) | ✅ (baris di luar cabangnya ditolak otomatis) |
| Export Excel | ✅ (semua data) | ✅ (cabang sendiri saja) |
| Kelola User (buat/nonaktifkan akun) | ✅ | ❌ |
| Lihat Log Data & Log Login | ✅ | ❌ |
| Akses jam kerja | 24 jam | 07.00–18.00 (bisa ajukan lembur) |

---

## Struktur Proyek

```
app/
  Http/Controllers/       Controller per fitur (Database, Diagram, Excel, UserManagement, dst.)
  Http/Middleware/        IsLogin, IsGuest, RestrictAccessHours (jam kerja)
  Models/                 SalesRecord, User, ActivityLog, UserActivityLog, OvertimeRequest
  Exports/, Imports/      Kelas Excel (export biasa, export summary, import dengan chunking)
database/
  migrations/             Riwayat perubahan skema, ditulis bertahap & terdokumentasi
  seeders/                RoleSeeder (role + permission)
resources/
  views/
    sales/                Dashboard, form tambah/edit, diagram, trash
    activity-log/         Log Data
    user-activity-log/    Log Login
    user-management/      Kelola User
    partials/             Komponen reusable (modal, ikon SVG, empty state, badge filter, dll.)
routes/web.php            Definisi route dengan middleware berlapis (login, jam akses, permission)
```

---

## Skema Database (Ringkas)

| Tabel | Fungsi |
|---|---|
| `users` | Akun, dengan `org_code` (cabang) dan `soft delete` untuk nonaktifkan akun |
| `databases` | Data transaksi penjualan (24 kolom), dengan `soft delete` + `deleted_reason` |
| `activity_logs` | Log perubahan data (create/update/delete/restore/import/export) |
| `user_activity_logs` | Log login/logout/percobaan gagal/pengajuan lembur |
| `overtime_requests` | Riwayat pengajuan akses lembur karyawan |
| `roles`, `permissions`, `model_has_roles`, `role_has_permissions` | Tabel bawaan Spatie Permission |

---

## Instalasi & Menjalankan Proyek

Panduan lengkap langkah demi langkah (termasuk setup XAMPP, konfigurasi PHP untuk file besar, pembuatan akun admin pertama, dan troubleshooting) ada di **[`SETUP.md`](./SETUP.md)**.

Ringkasan cepat:

```bash
composer create-project laravel/laravel:^12.0 visual-data
cd visual-data
# salin semua file dari project ini ke folder hasil create-project

composer require spatie/laravel-permission maatwebsite/excel
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
npm install alpinejs tailwindcss @tailwindcss/vite laravel-vite-plugin

php artisan migrate
php artisan db:seed --class=RoleSeeder
npm run build
php artisan serve
```

Akun admin pertama dibuat lewat `php artisan tinker` (lihat SETUP.md Bagian 16) — setelah itu, akun-akun berikutnya dibuat lewat menu **Kelola User** di aplikasi.

---

## Keamanan

- Password di-hash dengan bcrypt (`Hash::make()`), minimal 8 karakter + kombinasi huruf & angka
- Proteksi CSRF di seluruh form (`@csrf` + middleware bawaan Laravel)
- Query database 100% lewat Eloquent ORM/Query Builder — tidak ada raw SQL string concatenation, otomatis aman dari SQL Injection
- Output ke halaman otomatis di-escape (`{{ }}` Blade), aman dari XSS
- Rate limiting pada percobaan login (maks. 5 percobaan/menit)
- Percobaan login gagal tercatat untuk visibilitas potensi serangan brute-force
- Format SVG dilarang untuk foto profil (mencegah risiko script tersembunyi dalam file)
- Otorisasi diperiksa di level route/middleware (`can:...`), bukan hanya disembunyikan di tampilan — mencegah akses langsung lewat URL
- Checklist keamanan pra-deploy (matikan `APP_DEBUG`, aktifkan HTTPS, dsb.) didokumentasikan di SETUP.md

---

## Keterbatasan & Rencana Pengembangan

Beberapa hal yang secara sadar belum dikerjakan, untuk transparansi:

- Batas maksimal 5 akun Admin saat ini baru berupa **peringatan visual** di dashboard, belum benar-benar dicegah di level sistem (karena belum ada alur "promosikan user jadi admin" lewat UI — admin baru masih via `tinker`)
- Pengajuan akses lembur bersifat **self-service** (langsung disetujui otomatis, tercatat untuk audit), belum ada alur persetujuan berjenjang dari atasan
- Pembersihan data trash yang sudah lewat 24 jam masih berbasis *lazy purge* (saat halaman dibuka), belum pakai Laravel Task Scheduling + cron — cukup untuk skala penggunaan saat ini, tapi bisa ditingkatkan untuk deployment produksi
- Belum ada automated testing (unit/feature test)
- Kolom data transaksi lama (`Tanggal`, `AMMOUNT`, dst.) sengaja masih bertipe `string` demi kompatibilitas data lama — migrasi ke tipe data yang lebih akurat (`date`, `decimal`) adalah peningkatan yang bisa dilakukan terpisah

---

## Latar Belakang Proyek

Visual Data awalnya dibangun sebagai proyek Praktik Kerja Lapangan (PKL) di **Kisel**, anak perusahaan **Telkomsel** yang bergerak di bidang distribusi digital, dan meraih predikat **PKL Terbaik**. Versi yang ada di repositori ini adalah hasil rombak total dari basis kode aslinya (Laravel 8), dikerjakan ulang dengan arsitektur, keamanan, dan pengalaman pengguna yang jauh lebih matang — sekaligus jadi bahan pembelajaran nyata soal migrasi framework, penanganan data produksi berskala besar, dan desain sistem multi-tenant.
