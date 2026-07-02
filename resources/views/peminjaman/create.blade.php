@extends('layouts.admin')

@section('title', 'Catat Peminjaman')
@section('page-title', 'Catat Peminjaman Baru')
@section('page-subtitle', 'Buat transaksi peminjaman secara manual')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.peminjaman.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Anggota</label>
            <select name="id_anggota" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Pilih Anggota --</option>
                @foreach ($anggotaList as $a)
                    <option value="{{ $a->id_anggota }}">{{ $a->nama }} ({{ $a->username }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Buku</label>
            <select name="id_buku" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Pilih Buku --</option>
                @foreach ($bukuList as $b)
                    <option value="{{ $b->id_buku }}">{{ $b->judul }} (stok: {{ $b->stok }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
            <input type="number" name="jumlah_buku" value="1" min="1" required
                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                Simpan Peminjaman
            </button>
            <a href="{{ route('admin.peminjaman.index') }}" class="border border-slate-200 text-slate-600 text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
