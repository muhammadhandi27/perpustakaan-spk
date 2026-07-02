<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\SAWController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROUTE: HALAMAN AWAL
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| ROUTE: AUTENTIKASI (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTE: ADMIN (butuh login + role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn () => view('admin_dashboard'))->name('dashboard');

    // CRUD Kelola Buku
    Route::resource('buku', BukuController::class);

    // CRUD Kelola Anggota
    Route::resource('anggota', AnggotaController::class)->except(['show']);

    // Kelola Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

    // Kelola SPK (bobot kriteria + trigger hitung SAW)
    Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
    Route::put('/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('kriteria.update');
    Route::get('/spk/hitung-saw', [SAWController::class, 'hitungSAW'])->name('spk.hitung');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
});

/*
|--------------------------------------------------------------------------
| ROUTE: ANGGOTA (butuh login, role bebas — admin pun bisa akses jika perlu)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', fn () => view('member_dashboard'))->name('dashboard');
    Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])->name('riwayat');
    Route::post('/pinjam/{id_buku}', [PeminjamanController::class, 'pinjam'])->name('pinjam');
});

/*
|--------------------------------------------------------------------------
| ROUTE: DATA REKOMENDASI (dipakai fetch() di member_dashboard, semua user login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->get('/spk/rekomendasi', [SAWController::class, 'tampilkanRekomendasi'])
    ->name('rekomendasi.tampilkan');
