@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')
@section('page-subtitle', 'Data pengguna sistem perpustakaan')

@section('header-action')
    <a href="{{ route('admin.anggota.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Anggota
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">

    <div class="px-6 py-4 border-b border-slate-100">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama atau username..."
                class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Username</th>
                    <th class="px-6 py-3 text-left">No. HP</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($anggota as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-medium text-slate-800">{{ $item->nama }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $item->username }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $item->no_hp ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $item->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($item->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.anggota.edit', $item->id_anggota) }}" class="text-indigo-600 hover:underline text-xs font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.anggota.destroy', $item->id_anggota) }}" onsubmit="return confirm('Hapus anggota ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:underline text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data anggota.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100">
        {{ $anggota->links() }}
    </div>
</div>
@endsection
