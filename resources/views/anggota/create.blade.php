@extends('layouts.admin')

@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota Baru')
@section('page-subtitle', 'Buat akun anggota atau admin baru')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.anggota.store') }}" class="space-y-6">
        @csrf

        @include('anggota._form')

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                Simpan
            </button>
            <a href="{{ route('admin.anggota.index') }}" class="border border-slate-200 text-slate-600 text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
