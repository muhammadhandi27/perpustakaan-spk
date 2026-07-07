@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan Rekomendasi Buku')
@section('page-subtitle', 'Hasil akhir perhitungan SAW')

@section('header-action')
    <a href="{{ route('admin.laporan.exportPdf') }}"
       class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
        <i class="fa-solid fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
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
                @forelse ($rekomendasi as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-bold text-indigo-600">#{{ $item->ranking }}</td>
                        <td class="px-6 py-3 text-slate-700">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="px-6 py-3"><span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded-full">{{ $item->buku->kategori ?? '-' }}</span></td>
                        <td class="px-6 py-3 font-medium text-slate-800">{{ number_format($item->bobot_preferensi, 4) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada hasil perhitungan. Jalankan SAW terlebih dahulu di menu Kelola SPK.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
