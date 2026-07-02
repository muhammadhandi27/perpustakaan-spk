<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard Anggota') - Perpustakaan SPK</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</head>
<body class="bg-slate-50 font-sans">

<header class="bg-white border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book text-white"></i>
            </div>
            <span class="font-bold text-slate-800">Perpus SPK</span>
        </div>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            <a href="{{ route('anggota.dashboard') }}" class="{{ request()->routeIs('anggota.dashboard') ? 'text-indigo-600' : 'hover:text-indigo-600' }}">Beranda</a>
            <a href="{{ route('anggota.riwayat') }}" class="{{ request()->routeIs('anggota.riwayat') ? 'text-indigo-600' : 'hover:text-indigo-600' }}">Riwayat Pinjam</a>
        </nav>

        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-sm text-slate-600">{{ auth()->user()->nama ?? 'Anggota' }}</span>
            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-8 space-y-8">

    @if (session('success'))
        <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

@yield('scripts')
</body>
</html>
