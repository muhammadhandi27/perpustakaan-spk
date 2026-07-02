# Panduan Implementasi Lengkap
## Sistem Informasi Perpustakaan Berbasis Web dengan SPK Rekomendasi Buku (Metode SAW)

Panduan ini menjelaskan langkah demi langkah cara menjalankan 4 file yang sudah dibuat sebelumnya:
- `database_schema.sql`
- `SAWController.php`
- `admin_dashboard.html`
- `member_dashboard.html`

hingga menjadi aplikasi Laravel yang berjalan penuh di komputer Anda, siap untuk demo tugas kuliah.

---

## DAFTAR ISI
1. Persiapan Software (Prasyarat)
2. Instalasi Project Laravel Baru
3. Konfigurasi Database (.env)
4. Import Skema Database (SQL)
5. Membuat Model Eloquent
6. Menempatkan Controller SAW
7. Membuat Routing
8. Mengubah HTML Statis Menjadi Blade View
9. Menjalankan & Menguji Aplikasi
10. Alur Uji Coba (Testing Skenario Demo)
11. Troubleshooting Masalah Umum
12. Struktur Folder Akhir Project
13. Pengembangan Lanjutan (Opsional)

---

## 1. PERSIAPAN SOFTWARE (PRASYARAT)

Install terlebih dahulu software berikut di komputer Anda:

| Software | Versi Minimal | Cek dengan |
|---|---|---|
| PHP | 8.1+ | `php -v` |
| Composer | 2.x | `composer -v` |
| MySQL / MariaDB | 5.7+ / 10.x | `mysql --version` |
| Node.js & NPM (opsional, untuk build asset) | 18+ | `node -v` |

**Tools pendukung yang disarankan:**
- XAMPP / Laragon (Windows) — sudah termasuk PHP, MySQL, Apache
- phpMyAdmin atau DBeaver / TablePlus — untuk mengelola database secara visual
- VS Code — text editor
- Postman — untuk menguji endpoint API secara terpisah dari tampilan

> 💡 Jika Anda memakai **Laragon** atau **XAMPP**, PHP, Composer (biasanya perlu install manual), dan MySQL sudah tersedia dalam satu paket sehingga lebih mudah bagi pemula.

---

## 2. INSTALASI PROJECT LARAVEL BARU

Buka terminal / CMD, arahkan ke folder tempat Anda ingin menyimpan project (misal `htdocs` atau `www`), lalu jalankan:

```bash
composer create-project laravel/laravel perpustakaan-spk
cd perpustakaan-spk
```

Setelah selesai, coba jalankan server bawaan Laravel untuk memastikan instalasi berhasil:

```bash
php artisan serve
```

Buka browser ke `http://127.0.0.1:8000` — jika muncul halaman selamat datang Laravel, instalasi berhasil. Tekan `CTRL + C` di terminal untuk menghentikan server sementara.

---

## 3. KONFIGURASI DATABASE (.env)

Buat database baru terlebih dahulu (lewat phpMyAdmin atau terminal MySQL):

```sql
CREATE DATABASE db_perpustakaan_spk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

> Catatan: Nama database ini sudah otomatis dibuat juga oleh baris pertama `database_schema.sql` (`CREATE DATABASE IF NOT EXISTS ...`), jadi Anda boleh melewati langkah manual ini jika langsung import file SQL di langkah 4.

Buka file `.env` di root project Laravel, sesuaikan bagian koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan_spk
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial MySQL Anda (default XAMPP/Laragon biasanya `root` tanpa password).

---

## 4. IMPORT SKEMA DATABASE (SQL)

Ada 2 cara, pilih salah satu:

### Cara A — Import langsung via phpMyAdmin (paling mudah)
1. Buka `http://localhost/phpmyadmin`
2. Klik tab **Import**
3. Pilih file `database_schema.sql` yang sudah dibuat sebelumnya
4. Klik **Go** / **Kirim**
5. Pastikan 8 tabel (`buku`, `anggota`, `peminjaman`, `detail_peminjaman`, `kriteria`, `penilaian_buku`, `rekomendasi_buku`, `preferensi_anggota`) berhasil terbentuk beserta data dummy buku.

### Cara B — Import via terminal
```bash
mysql -u root -p < database_schema.sql
```
(kosongkan password jika tidak diset, cukup tekan Enter)

### Verifikasi
Jalankan query berikut untuk memastikan data buku sudah masuk:
```sql
USE db_perpustakaan_spk;
SELECT * FROM buku;
SELECT * FROM kriteria;
```
Anda harus melihat 6 baris data buku dan 4 baris kriteria (C1-C4).

---

## 5. MEMBUAT MODEL ELOQUENT

Laravel butuh "Model" sebagai perantara antara Controller dan tabel database. Buat 4 model berikut menggunakan Artisan:

```bash
php artisan make:model Buku
php artisan make:model Kriteria
php artisan make:model PenilaianBuku
php artisan make:model RekomendasiBuku
```

Kemudian isi masing-masing file di folder `app/Models/` sebagai berikut:

**`app/Models/Buku.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    protected $guarded = []; // semua kolom bisa diisi mass-assignment
}
```

**`app/Models/Kriteria.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';
    protected $guarded = [];
}
```

**`app/Models/PenilaianBuku.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianBuku extends Model
{
    protected $table = 'penilaian_buku';
    public $incrementing = false; // primary key composite, bukan auto-increment
    protected $guarded = [];

    // Karena composite key, nonaktifkan primaryKey tunggal bawaan Eloquent
    protected $primaryKey = null;
    public $timestamps = false;
}
```

**`app/Models/RekomendasiBuku.php`**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiBuku extends Model
{
    protected $table = 'rekomendasi_buku';
    protected $primaryKey = 'id_hasil';
    protected $guarded = [];
    public $timestamps = false;

    // Relasi ke tabel Buku (satu hasil rekomendasi merujuk ke satu buku)
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
```

> ⚠️ Model **Anggota**, **Peminjaman**, **Detail_Peminjaman**, dan **Preferensi_Anggota** belum dipakai controller SAW, tapi bisa dibuat dengan pola yang sama saat Anda mengembangkan fitur Kelola Anggota / Peminjaman.

---

## 6. MENEMPATKAN CONTROLLER SAW

1. Buat controller baru dengan Artisan (ini hanya membuat file kosong, nanti akan kita timpa):
```bash
php artisan make:controller SAWController
```

2. Buka file `app/Http/Controllers/SAWController.php` yang baru dibuat, **hapus semua isi bawaannya**, lalu **copy-paste seluruh isi file `SAWController.php`** yang sudah dibuat sebelumnya ke dalamnya. Pastikan bagian `namespace App\Http\Controllers;` dan `use` di bagian atas tetap sesuai.

3. Simpan file.

---

## 7. MEMBUAT ROUTING

Buka file `routes/web.php`, tambahkan route berikut di bagian bawah:

```php
<?php

use App\Http\Controllers\SAWController;
use Illuminate\Support\Facades\Route;

// Halaman utama Blade (opsional, lihat langkah 8)
Route::get('/admin/dashboard', function () {
    return view('admin_dashboard');
});

Route::get('/anggota/dashboard', function () {
    return view('member_dashboard');
});

// =====================================================
// ROUTE UNTUK SPK - METODE SAW
// =====================================================

// Menjalankan perhitungan SAW dari awal (hitung ulang & simpan ke DB)
Route::get('/spk/hitung-saw', [SAWController::class, 'hitungSAW']);

// Menampilkan hasil rekomendasi yang sudah tersimpan (dipakai front-end anggota)
Route::get('/spk/rekomendasi', [SAWController::class, 'tampilkanRekomendasi']);
```

> 📌 **Penjelasan:** `/spk/hitung-saw` dipanggil admin (misal lewat tombol "Jalankan Perhitungan SAW" di dashboard admin) untuk memproses ulang algoritma dan menyimpan hasil terbaru. `/spk/rekomendasi` dipanggil halaman anggota untuk **menampilkan** hasil tersebut tanpa menghitung ulang (lebih ringan/cepat).

---

## 8. MENGUBAH HTML STATIS MENJADI BLADE VIEW

File `admin_dashboard.html` dan `member_dashboard.html` yang sudah dibuat adalah HTML murni. Agar bisa dirender Laravel dan terhubung otomatis ke route Laravel (bukan hardcode `/spk/rekomendasi`), ubah menjadi Blade View:

1. Buat 2 file baru di `resources/views/`:
```bash
touch resources/views/admin_dashboard.blade.php
touch resources/views/member_dashboard.blade.php
```
(di Windows tanpa Git Bash, cukup buat file baru manual lewat VS Code)

2. **Copy-paste seluruh isi** `admin_dashboard.html` ke `admin_dashboard.blade.php`, dan seluruh isi `member_dashboard.html` ke `member_dashboard.blade.php`. Tidak perlu ada perubahan struktur — file `.blade.php` tetap bisa berisi HTML biasa.

3. **Khusus `member_dashboard.blade.php`**, ubah baris fetch API agar memakai helper route Laravel (lebih aman dari salah ketik path). Cari baris ini:

```javascript
const response = await fetch('/spk/rekomendasi');
```

Ganti menjadi (opsional, tapi lebih "Laravel-native"):

```javascript
const response = await fetch("{{ route('rekomendasi.tampilkan') }}");
```

Jika memakai cara ini, beri **nama route** di `routes/web.php`:
```php
Route::get('/spk/rekomendasi', [SAWController::class, 'tampilkanRekomendasi'])
    ->name('rekomendasi.tampilkan');
```

> Jika Anda ingin cara paling simpel tanpa mengubah JS sama sekali, **biarkan saja** `fetch('/spk/rekomendasi')` apa adanya — itu tetap akan berfungsi normal karena route-nya persis sama.

4. Simpan semua file.

---

## 9. MENJALANKAN & MENGUJI APLIKASI

Jalankan server Laravel:
```bash
php artisan serve
```

Buka browser dan akses:

| URL | Fungsi |
|---|---|
| `http://127.0.0.1:8000/admin/dashboard` | Halaman Dashboard Admin |
| `http://127.0.0.1:8000/anggota/dashboard` | Halaman Dashboard Anggota + Rekomendasi SAW |
| `http://127.0.0.1:8000/spk/hitung-saw` | Trigger perhitungan SAW (hasil JSON) |
| `http://127.0.0.1:8000/spk/rekomendasi` | Lihat hasil rekomendasi tersimpan (JSON) |

**Langkah pengujian yang disarankan:**
1. Akses `/spk/hitung-saw` terlebih dahulu di browser — pastikan muncul response JSON `"status": "success"` beserta array hasil ranking buku.
2. Cek tabel `rekomendasi_buku` di phpMyAdmin — harus terisi 6 baris (sesuai jumlah buku dummy), berurutan sesuai ranking.
3. Baru buka `/anggota/dashboard` — grid kartu rekomendasi akan otomatis memuat data dari langkah 1-2 lewat `fetch()`.

---

## 10. ALUR UJI COBA (TESTING SKENARIO DEMO)

Gunakan skenario ini saat presentasi agar dosen/penguji melihat sistem bekerja secara nyata:

1. **Tunjukkan data mentah** — buka tabel `buku` dan `penilaian_buku` di phpMyAdmin, jelaskan bahwa ini adalah matriks keputusan (X).
2. **Jalankan perhitungan** — akses `/spk/hitung-saw`, tunjukkan hasil JSON berisi nilai normalisasi & `v_i` tiap buku.
3. **Ubah data** — misal update `rating` salah satu buku di tabel `buku` (via phpMyAdmin) agar nilainya naik signifikan.
4. **Hitung ulang** — akses lagi `/spk/hitung-saw`, tunjukkan bahwa ranking buku tersebut ikut naik → membuktikan sistem benar-benar dinamis, bukan hardcode.
5. **Tampilkan ke anggota** — buka `/anggota/dashboard`, tunjukkan hasil ranking baru otomatis tampil di grid kartu.
6. **Uji tombol Pinjam** — klik tombol Pinjam pada salah satu kartu, tunjukkan modal konfirmasi muncul.

---

## 11. TROUBLESHOOTING MASALAH UMUM

| Gejala | Penyebab Umum | Solusi |
|---|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Nama DB di `.env` tidak sama dengan yang di-import | Samakan `DB_DATABASE` dengan nama database di phpMyAdmin |
| `Class "App\Models\Buku" not found` | Model belum dibuat / typo namespace | Pastikan `php artisan make:model Buku` sudah dijalankan dan namespace sesuai |
| Halaman `/anggota/dashboard` kosong / kartu tidak muncul | Endpoint `/spk/rekomendasi` belum pernah dipanggil (`rekomendasi_buku` masih kosong) | Akses dulu `/spk/hitung-saw` sekali agar tabel terisi |
| `CORS` error saat fetch | Biasanya muncul jika HTML dibuka langsung dari file (`file://`) bukan lewat `php artisan serve` | Selalu akses lewat `http://127.0.0.1:8000/...`, jangan double click file HTML langsung |
| Response JSON `"message": "Data penilaian buku tidak ditemukan."` | Tabel `buku` kosong atau `sinkronisasiPenilaianBuku()` gagal | Pastikan data dummy buku sudah ter-import (langkah 4) |
| Error `koneksi ditolak / Access denied for user 'root'` | Password MySQL di `.env` salah | Sesuaikan `DB_PASSWORD` dengan MySQL Anda |
| Tampilan Tailwind tidak muncul (halaman polos) | Koneksi internet mati (CDN Tailwind butuh internet) | Pastikan koneksi internet aktif, karena project ini memakai Tailwind via CDN, bukan build lokal |

---

## 12. STRUKTUR FOLDER AKHIR PROJECT

Setelah semua langkah selesai, struktur folder relevan akan terlihat seperti ini:

```
perpustakaan-spk/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── SAWController.php        <-- isi dari file yang sudah dibuat
│   └── Models/
│       ├── Buku.php
│       ├── Kriteria.php
│       ├── PenilaianBuku.php
│       └── RekomendasiBuku.php
├── resources/
│   └── views/
│       ├── admin_dashboard.blade.php    <-- isi dari admin_dashboard.html
│       └── member_dashboard.blade.php   <-- isi dari member_dashboard.html
├── routes/
│   └── web.php                          <-- tambahkan route SPK
├── .env                                  <-- konfigurasi database
└── database_schema.sql                   <-- sudah di-import ke MySQL
```

---

## 13. PENGEMBANGAN LANJUTAN (OPSIONAL)

Jika waktu memungkinkan, beberapa hal berikut bisa ditambahkan agar laporan/proyek lebih lengkap:

1. **Autentikasi Login** — gunakan `Laravel Breeze` (`composer require laravel/breeze`) untuk membuat sistem login Admin & Anggota terpisah.
2. **CRUD Buku, Anggota, Peminjaman** — buat controller & view tambahan untuk menu "Kelola Buku", "Kelola Anggota", "Kelola Peminjaman" di sidebar admin yang saat ini masih berupa tautan statis.
3. **Migration Laravel** — sebagai alternatif `database_schema.sql`, Anda bisa membuat migration resmi Laravel (`php artisan make:migration create_buku_table`) agar skema bisa di-*version control* lewat Git.
4. **Fitur Preferensi Anggota** — memanfaatkan tabel `preferensi_anggota` untuk personalisasi bobot kriteria per anggota, sehingga tiap anggota bisa punya urutan rekomendasi berbeda (pengembangan dari SAW standar).
5. **Export Laporan** — tambahkan fitur export hasil ranking SAW ke PDF/Excel (misal pakai `barryvdh/laravel-dompdf` atau `maatwebsite/excel`) untuk menu "Laporan" di sidebar.
6. **Validasi Form** — tambahkan `Request` class Laravel untuk validasi input saat admin menambah/mengubah data buku atau nilai kriteria.

---

### Ringkasan Perintah Cepat (Cheat Sheet)

```bash
# 1. Buat project
composer create-project laravel/laravel perpustakaan-spk
cd perpustakaan-spk

# 2. Buat model
php artisan make:model Buku
php artisan make:model Kriteria
php artisan make:model PenilaianBuku
php artisan make:model RekomendasiBuku

# 3. Buat controller
php artisan make:controller SAWController

# 4. Jalankan server
php artisan serve
```

Selamat mencoba dan semoga sukses untuk demo tugas SPK-nya! 🎓
