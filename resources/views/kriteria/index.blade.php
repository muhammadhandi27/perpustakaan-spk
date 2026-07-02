@extends('layouts.admin')

@section('title', 'Kelola SPK')
@section('page-title', 'Kelola SPK (SAW)')
@section('page-subtitle', 'Atur bobot kriteria & jalankan perhitungan')

@section('header-action')
    <a href="{{ route('admin.spk.hitung') }}" target="_blank"
       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
        <i class="fa-solid fa-play"></i> Jalankan Perhitungan SAW
    </a>
@endsection

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="font-semibold text-slate-800">Bobot Kriteria</h2>
        <p class="text-xs text-slate-500">
            Total bobot saat ini:
            <span class="font-medium {{ $totalBobot == 1 ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($totalBobot, 2) }}</span>
            (idealnya = 1.00)
        </p>
    </div>

    <div class="divide-y divide-slate-100">
        @foreach ($kriteria as $item)
            <form method="POST" action="{{ route('admin.kriteria.update', $item->id_kriteria) }}"
                  class="px-6 py-4 flex items-center gap-4">
                @csrf
                @method('PUT')

                <div class="w-14 text-sm font-bold text-indigo-600">{{ $item->kode_kriteria }}</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $item->nama_kriteria }}</p>
                    <p class="text-xs text-slate-500">Jenis: {{ $item->jenis }}</p>
                </div>
                <input type="number" step="0.01" min="0" max="1" name="bobot" value="{{ $item->bobot }}"
                    class="w-24 border border-slate-200 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="text-indigo-600 hover:underline text-xs font-medium">Simpan</button>
            </form>
        @endforeach
    </div>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700 flex items-start gap-3">
    <i class="fa-solid fa-circle-info mt-0.5"></i>
    <p>Setelah mengubah bobot, klik tombol <strong>"Jalankan Perhitungan SAW"</strong> di atas agar ranking rekomendasi buku ikut diperbarui.</p>
</div>

@endsection
