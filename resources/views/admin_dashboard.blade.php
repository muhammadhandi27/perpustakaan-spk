@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan aktivitas perpustakaan hari ini')

@section('header-action')
    <a href="{{ route('admin.spk.hitung') }}" target="_blank"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
        <i class="fa-solid fa-play"></i> Jalankan Perhitungan SAW
    </a>
@endsection

@section('content')

@php
    // Statistik ringkas diambil langsung dari database
    $totalBuku = \App\Models\Buku::count();
    $totalAnggota = \App\Models\Anggota::where('role', 'anggota')->count();
    $peminjamanBulanIni = \App\Models\Peminjaman::whereMonth('tanggal_pinjam', now()->month)->count();
    $peminjamanAktif = \App\Models\Peminjaman::where('status', 'Dipinjam')->count();
    $topRekomendasi = \App\Models\RekomendasiBuku::with('buku')->orderBy('ranking')->take(3)->get();
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <div class="w-11 h-11 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mb-3">
            <i class="fa-solid fa-book"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $totalBuku }}</p>
        <p class="text-sm text-slate-500">Total Buku</p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <div class="w-11 h-11 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-3">
            <i class="fa-solid fa-users"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $totalAnggota }}</p>
        <p class="text-sm text-slate-500">Total Anggota</p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <div class="w-11 h-11 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center mb-3">
            <i class="fa-solid fa-right-left"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $peminjamanBulanIni }}</p>
        <p class="text-sm text-slate-500">Peminjaman Bulan Ini ({{ $peminjamanAktif }} aktif)</p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
        <div class="w-11 h-11 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-3">
            <i class="fa-solid fa-star"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $topRekomendasi->count() }}</p>
        <p class="text-sm text-slate-500">Buku Rekomendasi Teratas</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-slate-800">Hasil Perhitungan SPK - Metode SAW</h2>
            <p class="text-xs text-slate-500">3 buku dengan nilai preferensi tertinggi</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="text-sm text-indigo-600 font-medium hover:underline">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Ranking</th>
                    <th class="px-6 py-3 text-left">Judul Buku</th>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-6 py-3 text-left">Nilai Preferensi (Vi)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($topRekomendasi as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-bold text-indigo-600">#{{ $item->ranking }}</td>
                        <td class="px-6 py-3 text-slate-700">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded-full">{{ $item->buku->kategori ?? '-' }}</span></td>
                        <td class="px-6 py-3 font-medium text-slate-800">{{ number_format($item->bobot_preferensi, 4) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada hasil. Klik "Jalankan Perhitungan SAW" di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
