# Panduan Integrasi Fitur Baru
## Autentikasi, CRUD Buku/Anggota/Peminjaman, Kelola SPK, dan Laporan PDF

Panduan ini melanjutkan `panduan_implementasi.md` sebelumnya. Pastikan Anda sudah menyelesaikan langkah 1-9 di panduan tersebut (project Laravel berjalan, database ter-import, `SAWController` & route dasar sudah ada) sebelum lanjut ke sini.

File pendukung: **`pengembangan_fitur.zip`** — berisi seluruh file baru dengan struktur folder Laravel yang sudah benar, tinggal disalin ke project Anda.

---

## DAFTAR ISI
1. Ekstrak & Salin File
2. Install Package Tambahan (dompdf)
3. Migration & Seeder Akun
4. Konfigurasi Auth (`config/auth.php`)
5. Registrasi Middleware
6. Timpa `routes/web.php`
7. Sesuaikan Blade Layout Lama
8. Jalankan & Login
9. Alur Uji Coba Lengkap
10. Troubleshooting Tambahan
11. Struktur Folder Akhir

---

## 1. EKSTRAK & SALIN FILE

Ekstrak `pengembangan_fitur.zip`, lalu salin **seluruh isinya** ke root project Laravel Anda (folder `perpustakaan-spk/`), timpa jika diminta konfirmasi:

```
pengembangan_fitur/
├── app/
│   ├── Http/Controllers/       → salin ke app/Http/Controllers/
│   ├── Http/Middleware/        → salin ke app/Http/Middleware/
│   └── Models/                 → salin ke app/Models/
├── database/
│   ├── migrations/             → salin ke database/migrations/
│   └── seeders/                → salin ke database/seeders/
├── resources/views/            → salin ke resources/views/ (timpa admin_dashboard.blade.php & member_dashboard.blade.php lama)
└── routes/web.php              → timpa routes/web.php lama
```

> 💡 Tips: paling cepat lewat file explorer — buka folder hasil ekstrak dan folder project Laravel berdampingan, lalu drag & drop foldernya (`app`, `database`, `resources`, `routes`) agar otomatis merge/timpa ke lokasi yang sama.

---

## 2. INSTALL PACKAGE TAMBAHAN (dompdf)

Fitur **Laporan → Export PDF** membutuhkan package `barryvdh/laravel-dompdf`. Install lewat Composer:

```bash
composer require barryvdh/laravel-dompdf
```

Tidak perlu konfigurasi tambahan — package ini auto-discover di Laravel modern.

---

## 3. MIGRATION & SEEDER AKUN

Jalankan migration baru untuk menambahkan kolom `role` dan `remember_token` ke tabel `anggota`:

```bash
php artisan migrate
```

Jika muncul error `Migration table not found`, jalankan dulu:
```bash
php artisan migrate:install
```

Kemudian jalankan seeder untuk membuat akun demo (1 admin + 1 anggota):

```bash
php artisan db:seed --class=AdminSeeder
```

**Akun demo yang terbentuk:**

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Anggota | `rina` | `anggota123` |

> ⚠️ Ganti password ini sebelum digunakan di luar keperluan demo tugas kuliah.

---

## 4. KONFIGURASI AUTH (`config/auth.php`)

Laravel secara default memakai model `App\Models\User` untuk autentikasi. Karena project ini memakai tabel `anggota` sebagai model user, buka `config/auth.php` dan ubah baris berikut:

**Cari:**
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

**Ubah menjadi:**
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Anggota::class,
    ],
],
```

> Jika project Laravel bawaan Anda masih memiliki `app/Models/User.php` dan tabel `users` (dari migration default), Anda **boleh membiarkannya** — cukup tidak dipakai. Atau hapus saja migration `xxxx_create_users_table.php` bawaan jika ingin lebih rapi (opsional, tidak wajib).

---

## 5. REGISTRASI MIDDLEWARE

Cara mendaftarkan `AdminMiddleware` **berbeda tergantung versi Laravel Anda**:

### Jika project Anda Laravel 10 (masih ada file `app/Http/Kernel.php`)
Buka `app/Http/Kernel.php`, cari array `$middlewareAliases` (atau `$routeMiddleware` di versi lebih lama), tambahkan baris:

```php
protected $middlewareAliases = [
    // ... alias bawaan lainnya
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
```

### Jika project Anda Laravel 11+ (tidak ada `Kernel.php`, hanya `bootstrap/app.php`)
Buka `bootstrap/app.php`, tambahkan di dalam method `withMiddleware()`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

> 💡 Cek versi Laravel Anda dengan `php artisan --version` jika ragu.

---

## 6. TIMPA `routes/web.php`

Pastikan file `routes/web.php` sudah tertimpa oleh versi baru dari `pengembangan_fitur.zip` (langkah 1). File ini sudah mencakup seluruh route: auth, admin (buku/anggota/peminjaman/kriteria/laporan), dan anggota.

Verifikasi dengan:
```bash
php artisan route:list
```
Anda harus melihat route seperti `admin.buku.index`, `admin.kriteria.update`, `anggota.pinjam`, dll.

---

## 7. SESUAIKAN BLADE LAYOUT LAMA

Jika sebelumnya Anda sudah sempat mengubah `admin_dashboard.blade.php` atau `member_dashboard.blade.php` secara manual (misal saat memperbaiki CDN Tailwind), **abaikan versi lama tersebut** — versi baru dari zip sudah menggunakan CDN yang benar (`https://cdn.tailwindcss.com`) DAN sudah terhubung ke layout (`@extends('layouts.admin')` / `@extends('layouts.member')`), data asli dari database, serta tombol Pinjam yang benar-benar memanggil backend.

---

## 8. JALANKAN & LOGIN

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` — Anda akan diarahkan ke halaman **Login**.

- Login sebagai **admin** (`admin` / `admin123`) → masuk ke `/admin/dashboard`, sidebar lengkap dengan Kelola Buku, Anggota, Peminjaman, SPK, Laporan.
- Login sebagai **anggota** (`rina` / `anggota123`) → masuk ke `/anggota/dashboard`, tampil grid rekomendasi buku hasil SAW + bisa klik "Pinjam".

---

## 9. ALUR UJI COBA LENGKAP (SKENARIO DEMO)

1. **Login sebagai admin** → buka menu **Kelola Buku**, tambah 1 buku baru.
2. Buka menu **Kelola SPK** → cek bobot kriteria (harus total = 1.00), lalu klik **"Jalankan Perhitungan SAW"** (akan membuka tab baru berisi JSON hasil).
3. Buka menu **Laporan** → lihat tabel ranking, coba klik **Export PDF** → file PDF terunduh otomatis.
4. **Logout**, lalu **Login sebagai anggota** (`rina`) → lihat halaman rekomendasi menampilkan hasil ranking yang sama dengan Laporan admin.
5. Klik tombol **Pinjam** pada salah satu buku → konfirmasi → cek stok buku otomatis berkurang (lihat lagi di Kelola Buku sebagai admin).
6. Sebagai anggota, buka menu **Riwayat Pinjam** → transaksi yang baru saja dibuat harus muncul dengan status "Dipinjam".
7. **Login lagi sebagai admin** → buka **Kelola Peminjaman** → klik **"Tandai Kembali"** pada transaksi tersebut → status berubah jadi "Dikembalikan" dan stok buku otomatis bertambah kembali.
8. (Opsional) Coba **Daftar akun baru** lewat halaman Register (`/register`) untuk menunjukkan alur pendaftaran mandiri anggota.

---

## 10. TROUBLESHOOTING TAMBAHAN

| Gejala | Penyebab | Solusi |
|---|---|---|
| `Target class [admin] does not exist` | Middleware belum didaftarkan | Ulangi langkah 5 sesuai versi Laravel Anda |
| Setelah login admin, malah diarahkan ke halaman anggota | Kolom `role` user tersebut bukan `'admin'` | Cek tabel `anggota` di phpMyAdmin, pastikan `role = 'admin'` untuk akun tsb |
| Error `Class "Barryvdh\DomPDF\Facade\Pdf" not found` | Package dompdf belum terinstall | Jalankan ulang `composer require barryvdh/laravel-dompdf` |
| Tombol "Pinjam" gagal, muncul alert "Terjadi kesalahan jaringan" | CSRF token tidak terbaca / sesi login habis | Refresh halaman (login ulang jika perlu) |
| Error `SQLSTATE... Unknown column 'role'` | Migration baru belum dijalankan | Jalankan `php artisan migrate` |
| `Route [admin.dashboard] not defined` | `routes/web.php` belum tertimpa versi baru | Ulangi langkah 1 & 6 |
| Halaman Kelola Anggota/Buku menampilkan data tapi tombol Edit error 404 | Nama parameter route-model-binding tidak cocok | Pastikan file controller yang dipakai adalah versi dari zip (bukan versi lama Anda) |
| PDF hasil export tampilan berantakan/kosong | dompdf tidak mendukung Tailwind CDN | Sudah diantisipasi — `laporan/pdf.blade.php` sengaja pakai CSS manual, bukan Tailwind. Jangan diubah ke Tailwind. |

---

## 11. STRUKTUR FOLDER AKHIR (setelah integrasi penuh)

```
perpustakaan-spk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SAWController.php
│   │   │   ├── AuthController.php
│   │   │   ├── BukuController.php
│   │   │   ├── AnggotaController.php
│   │   │   ├── PeminjamanController.php
│   │   │   ├── KriteriaController.php
│   │   │   └── LaporanController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── Anggota.php        (Authenticatable)
│       ├── Buku.php
│       ├── Kriteria.php
│       ├── PenilaianBuku.php
│       ├── RekomendasiBuku.php
│       ├── Peminjaman.php
│       └── DetailPeminjaman.php
├── database/
│   ├── migrations/
│   │   └── 2026_01_01_000001_add_role_to_anggota_table.php
│   └── seeders/
│       └── AdminSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── admin.blade.php
│   │   └── member.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── buku/          (index, create, edit, _form)
│   ├── anggota/       (index, create, edit, _form)
│   ├── peminjaman/    (index, create, riwayat)
│   ├── kriteria/      (index)
│   ├── laporan/       (index, pdf)
│   ├── admin_dashboard.blade.php
│   └── member_dashboard.blade.php
├── routes/web.php
└── config/auth.php    (sudah disesuaikan)
```

---

### Ringkasan Perintah Cepat (Cheat Sheet)

```bash
# 1. Install package PDF
composer require barryvdh/laravel-dompdf

# 2. Migration & seeder
php artisan migrate
php artisan db:seed --class=AdminSeeder

# 3. Jalankan server
php artisan serve
```

Login demo: **admin / admin123** (Admin) atau **rina / anggota123** (Anggota) di `http://127.0.0.1:8000/login`.

Selamat mencoba — sistem sekarang sudah mencakup autentikasi, CRUD penuh, dan alur SPK end-to-end untuk demo tugas Anda! 🎓
