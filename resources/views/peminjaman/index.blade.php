@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')
@section('page-title', 'Kelola Peminjaman')
@section('page-subtitle', 'Transaksi peminjaman buku anggota')

@section('header-action')
    <a href="{{ route('admin.peminjaman.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Catat Peminjaman
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">

    <div class="px-6 py-4 border-b border-slate-100 flex gap-2">
        @foreach (['' => 'Semua', 'Dipinjam' => 'Dipinjam', 'Dikembalikan' => 'Dikembalikan', 'Terlambat' => 'Terlambat'] as $value => $label)
            <a href="{{ request()->fullUrlWithQuery(['status' => $value]) }}"
               class="text-xs px-3 py-1.5 rounded-full {{ $status === $value ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Anggota</th>
                    <th class="px-6 py-3 text-left">Buku</th>
                    <th class="px-6 py-3 text-left">Tgl Pinjam</th>
                    <th class="px-6 py-3 text-left">Tgl Kembali</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($peminjaman as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $item->anggota->nama ?? '-' }}</td>
                        <td class="px-6 py-3 text-slate-600">
                            @foreach ($item->detail as $d)
                                <div>{{ $d->buku->judul ?? '-' }} <span class="text-xs text-slate-400">x{{ $d->jumlah_buku }}</span></div>
                            @endforeach
                        </td>
                        <td class="px-6 py-3 text-slate-600">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-3">
                            @php
                                $warna = match($item->status) {
                                    'Dipinjam' => 'bg-amber-100 text-amber-700',
                                    'Dikembalikan' => 'bg-emerald-100 text-emerald-700',
                                    default => 'bg-rose-100 text-rose-700',
                                };
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full {{ $warna }}">{{ $item->status }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if ($item->status === 'Dipinjam')
                                <form method="POST" action="{{ route('admin.peminjaman.kembalikan', $item->id_peminjaman) }}">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:underline text-xs font-medium">Tandai Kembali</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100">
        {{ $peminjaman->links() }}
    </div>
</div>
@endsection
