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
| HALAMAN AWAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTENTIKASI
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | Dashboard
        */
        Route::view('/dashboard', 'admin.dashboard')
            ->name('dashboard');

        /*
        | Kelola Buku
        */
        Route::resource('buku', BukuController::class);

        /*
        | Kelola Anggota
        */
        Route::resource('anggota', AnggotaController::class)
            ->except(['show'])
            ->parameters([
                'anggota' => 'anggota',
            ]);

        /*
        | Kelola Peminjaman
        */
        Route::get('peminjaman', [PeminjamanController::class, 'index'])
            ->name('peminjaman.index');

        Route::get('peminjaman/create', [PeminjamanController::class, 'create'])
            ->name('peminjaman.create');

        Route::post('peminjaman', [PeminjamanController::class, 'store'])
            ->name('peminjaman.store');

        Route::post(
            'peminjaman/{peminjaman}/kembalikan',
            [PeminjamanController::class, 'kembalikan']
        )->name('peminjaman.kembalikan');

        /*
        | Kelola Bobot Kriteria
        */
        Route::get('kriteria', [KriteriaController::class, 'index'])
            ->name('kriteria.index');

        Route::put('kriteria/{kriteria}', [KriteriaController::class, 'update'])
            ->name('kriteria.update');

        /*
        | Hitung SAW
        */
        Route::get('spk/hitung-saw', [SAWController::class, 'hitungSAW'])
            ->name('spk.hitung');

        /*
        | Laporan
        */
        Route::get('laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])
            ->name('laporan.exportPdf');
    });


/*
|--------------------------------------------------------------------------
| MEMBER / ANGGOTA
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('anggota')
    ->name('anggota.')
    ->group(function () {

        Route::view('/dashboard', 'member.dashboard')
            ->name('dashboard');

        Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])
            ->name('riwayat');

        Route::post('/pinjam/{id_buku}', [PeminjamanController::class, 'pinjam'])
            ->name('pinjam');
    });


/*
|--------------------------------------------------------------------------
| API REKOMENDASI SAW
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->get('/spk/rekomendasi', [SAWController::class, 'tampilkanRekomendasi'])
    ->name('rekomendasi.tampilkan');