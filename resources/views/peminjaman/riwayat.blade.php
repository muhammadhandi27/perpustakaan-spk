@extends('layouts.member')

@section('title', 'Riwayat Pinjam')

@section('content')
<div>
    <h1 class="text-lg font-bold text-slate-800 mb-1">Riwayat Peminjaman</h1>
    <p class="text-sm text-slate-500 mb-6">Daftar buku yang pernah Anda pinjam</p>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Buku</th>
                        <th class="px-6 py-3 text-left">Tgl Pinjam</th>
                        <th class="px-6 py-3 text-left">Tgl Kembali</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 text-slate-700">
                                @foreach ($item->detail as $d)
                                    <div>{{ $d->buku->judul ?? '-' }}</div>
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
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Anda belum pernah meminjam buku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>
@endsection
