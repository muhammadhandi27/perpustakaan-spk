# Sistem Informasi Perpustakaan Berbasis Web dengan SPK Rekomendasi Buku (Metode SAW)

Sistem perpustakaan berbasis Laravel dengan fitur rekomendasi buku otomatis menggunakan **Sistem Pendukung Keputusan (SPK) metode SAW (Simple Additive Weighting)**.

## ✨ Fitur

- 🔐 Autentikasi (Login/Register) — role Admin & Anggota
- 📚 CRUD Kelola Buku
- 👥 CRUD Kelola Anggota
- 🔄 Kelola Peminjaman (catat pinjam, tandai kembali, riwayat)
- ⚖️ Kelola SPK — atur bobot kriteria & jalankan perhitungan SAW
- 📄 Laporan hasil rekomendasi (tampilan web + export PDF)
- 🎯 Dashboard Anggota menampilkan rekomendasi buku hasil SAW (card interaktif + tombol Pinjam)

## 🧮 Kriteria SPK

| Kode | Kriteria | Bobot | Jenis |
|---|---|---|---|
| C1 | Rating Buku | 0.30 | Benefit |
| C2 | Tahun Terbit | 0.25 | Benefit |
| C3 | Jumlah Peminjaman | 0.25 | Benefit |
| C4 | Kategori Buku | 0.20 | Benefit |

**Konversi Kategori (C4):** Programming=4, Artificial Intelligent=3, Sains=3, Self-Improvement=2, Sosial=2, Fiksi=1

## 🛠️ Tech Stack

- Laravel (PHP 8.1+)
- MySQL
- Tailwind CSS (via CDN)
- Font Awesome

---

## 🚀 Cara Menjalankan Project Ini

### 1. Clone repo & masuk ke folder project
```bash
git clone <url-repo-ini>.git perpustakaan-spk
cd perpustakaan-spk
```

### 2. Install dependency PHP
```bash
composer install
composer require barryvdh/laravel-dompdf
```

### 3. Siapkan file environment
```bash
cp .env.example .env
php artisan key:generate
```
Buka `.env`, sesuaikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dengan kredensial MySQL Anda.

### 4. Buat database & import skema
Buat database kosong terlebih dahulu (nama harus sama dengan `DB_DATABASE` di `.env`), lalu import:
```bash
mysql -u root -p db_perpustakaan_spk < database_schema.sql
```
(atau import lewat phpMyAdmin: tab **Import** → pilih `database_schema.sql`)

### 5. Jalankan migration & seeder
```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

### 6. Konfigurasi tambahan (WAJIB, tidak otomatis lewat migration)

**a) `config/auth.php`** — ubah model user default:
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Anggota::class,   // ganti dari App\Models\User::class
    ],
],
```

**b) Daftarkan middleware `admin`**

Jika ada `app/Http/Kernel.php` (Laravel 10):
```php
protected $middlewareAliases = [
    // ...
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

Jika tidak ada `Kernel.php`, pakai `bootstrap/app.php` (Laravel 11+):
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### 7. Jalankan server
```bash
php artisan serve
```
Buka `http://127.0.0.1:8000`

---

## 👤 Akun Demo

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Anggota | `rina` | `anggota123` |

> Ganti password ini jika project di-deploy ke luar lingkungan demo/tugas kuliah.

---

## 📁 Struktur Folder Penting

```
app/Http/Controllers/
├── SAWController.php        # Logika perhitungan SPK metode SAW
├── AuthController.php       # Login, Register, Logout
├── BukuController.php       # CRUD Buku
├── AnggotaController.php    # CRUD Anggota
├── PeminjamanController.php # Kelola & proses peminjaman
├── KriteriaController.php   # Atur bobot kriteria SPK
└── LaporanController.php    # Laporan & export PDF

app/Models/
├── Buku.php, Kriteria.php, PenilaianBuku.php, RekomendasiBuku.php
├── Anggota.php (Authenticatable — model login)
├── Peminjaman.php, DetailPeminjaman.php

resources/views/
├── layouts/          # Layout reusable (sidebar admin, navbar anggota)
├── auth/             # Login & Register
├── buku/, anggota/, peminjaman/, kriteria/, laporan/
├── admin_dashboard.blade.php
└── member_dashboard.blade.php

database_schema.sql   # Skema database lengkap + data dummy
docs/                 # Panduan setup & troubleshooting lebih detail
```

## 📖 Alur Kerja SPK (SAW)

1. Admin menambah/edit data buku (rating, tahun terbit, jumlah pinjam, kategori) lewat menu **Kelola Buku**.
2. Admin membuka menu **Kelola SPK** untuk memeriksa/mengatur bobot kriteria (default sudah sesuai laporan: 0.30/0.25/0.25/0.20).
3. Admin klik **"Jalankan Perhitungan SAW"** — sistem otomatis:
   - Menyalin data buku ke matriks keputusan (`penilaian_buku`)
   - Menormalisasi nilai (rumus benefit: `r_ij = x_ij / max(x_j)`)
   - Menghitung nilai preferensi `Vi = Σ(bobot × r_ij)`
   - Menyimpan hasil ranking ke `rekomendasi_buku`
4. Hasil ranking otomatis tampil di **Dashboard Admin**, **Laporan**, dan **Dashboard Anggota**.
5. Anggota bisa langsung klik tombol **Pinjam** pada buku rekomendasi.

> ⚠️ **Penting:** Setiap kali data buku berubah, ulangi langkah 3 (jalankan ulang SAW) agar ranking tetap sesuai data terbaru.

## 🩺 Troubleshooting Cepat

Lihat `docs/panduan_implementasi.md` dan `docs/panduan_integrasi_fitur_baru.md` untuk daftar lengkap troubleshooting (error database, middleware, dompdf, dll), serta `docs/query_diagnostik_saw.sql` jika hasil perhitungan SAW terlihat janggal (misal banyak buku bernilai preferensi sama).

---

## 📜 Lisensi

Project ini dibuat untuk keperluan tugas kuliah Sistem Pendukung Keputusan (SPK) — Prodi Teknik Informatika.
