# Visual Data — Sales Monitoring System

Aplikasi web internal untuk monitoring dan visualisasi data penjualan, dibangun sebagai proyek Praktik Kerja Lapangan (PKL) di **Kisel**, anak perusahaan **Telkomsel** yang bergerak di bidang distribusi produk digital. Proyek ini meraih predikat **PKL Terbaik**.

---

## Ringkasan

Visual Data membantu tim internal mencatat, memfilter, dan menganalisis data transaksi penjualan lintas customer dan cabang (`ORG_CODE`) — mulai dari input data manual, import massal lewat Excel, sampai visualisasi tren transaksi dalam bentuk diagram.

---

## Fitur

### Autentikasi & Role
- Login, register, logout
- Role `admin` dan `user` (dikelola lewat Spatie Laravel-Permission)
- Admin punya akses penuh (tambah/edit/hapus data, import Excel); user biasa bisa lihat data dan export

### Data Penjualan
- Tabel data dengan pagination (jumlah baris per halaman bisa diatur)
- Filter berdasarkan rentang tanggal dan nama customer
- Form tambah data dengan dropdown pilihan yang diambil dari data yang sudah ada (ORG_CODE, kode produk, tipe mitra, tipe bisnis, dan lain-lain — 24 kolom data per transaksi)
- Update dan hapus data

### Import & Export Excel
- Import data transaksi dari file Excel (`.xlsx`)
- Export data ke Excel dengan filter nama customer, dua tipe export (ringkas & lengkap)

### Diagram & Visualisasi
- Diagram jumlah transaksi per customer (tahunan) memakai Chart.js
- Diagram jumlah transaksi per customer pada tanggal tertentu (harian)

### Profile
- Lihat dan update profil (nama, email, foto)

---

## Tumpukan Teknologi

| Kategori | Teknologi |
|---|---|
| Backend | PHP, Laravel 8 |
| Database | MySQL |
| Frontend | AdminLTE 3 (Bootstrap), jQuery, Alpine.js |
| Otorisasi | Spatie Laravel-Permission |
| Excel | Maatwebsite/Laravel-Excel |
| Visualisasi | Chart.js |
| Komponen UI tambahan | DataTables, SweetAlert2, Select2, Flatpickr, Toastr |

---

## Struktur Data Utama

Tabel `databases` menyimpan data transaksi dengan kolom-kolom berikut: `Tanggal`, `ORG_CODE`, `NAMA_CUSTOMER`, `KODE_PRODUK`, `AMMOUNT`, `HARGA_JUAL`, `TRX`, `TYPE_MITRA`, `AMMOUNT_FIX`, `PRODUK_FIX`, `BUCKET_NAME`, `Type_Produk`, `TYPE_BISNIS`, `REV_INPPN`, `PAJAK`, `REV_EXPPN`, `HPP`, `TOTAL_HPP_INPPN`, `TOTAL_HPP_EXPPN`, `Margin_INPPN`, `Margin_EXPPN`, `Hari`, `Bulan`, `KET_PROD`.

---

## Instalasi

```bash
git clone <url-repo-ini>
cd visual-data
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
# sesuaikan koneksi database di .env
php artisan migrate
php artisan serve
```

---

## Konteks Pengembangan

Proyek ini dikerjakan selama masa PKL sebagai pembelajaran pertama membangun aplikasi web full-stack yang benar-benar dipakai secara internal — mulai dari desain skema database, autentikasi & role, sampai fitur import/export data dalam skala nyata. Sebagai proyek yang dikerjakan dalam waktu terbatas di masa SMK, ada beberapa hal yang belum ideal dari sisi best practice (validasi form, struktur kode, dependency yang menumpuk) — poin-poin inilah yang kemudian jadi bahan evaluasi dan dasar untuk pengembangan ulang (rebuild) versi berikutnya menggunakan Laravel 12 dan arsitektur yang lebih matang.

> **Catatan:** repositori ini merepresentasikan versi awal/original proyek (sebagaimana disubmit saat PKL). Untuk versi hasil rebuild total dengan arsitektur modern, kontrol akses multi-cabang, audit log, dan berbagai peningkatan lainnya, lihat proyek **Visual Data v2** (Laravel 12).
