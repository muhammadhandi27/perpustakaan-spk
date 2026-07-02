@extends('layouts.admin')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Data Buku')
@section('page-subtitle', $buku->judul)

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.buku.update', $buku->id_buku) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('buku._form')

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                Update Buku
            </button>
            <a href="{{ route('admin.buku.index') }}" class="border border-slate-200 text-slate-600 text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
