<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard Admin') - Perpustakaan SPK</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</head>
<body class="bg-slate-100 font-sans">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-slate-200 flex flex-col fixed lg:static inset-y-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book text-white"></i>
            </div>
            <div>
                <p class="font-bold text-white leading-tight">Perpus SPK</p>
                <p class="text-xs text-slate-400">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>

            @php
                // Helper kecil untuk menandai menu aktif berdasarkan nama route saat ini
                $isActive = fn ($routeName) => request()->routeIs($routeName)
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'hover:bg-slate-800';
            @endphp

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.dashboard') }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.buku.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.buku.*') }}">
                <i class="fa-solid fa-book-open w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Buku</span>
            </a>

            <a href="{{ route('admin.anggota.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.anggota.*') }}">
                <i class="fa-solid fa-users w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Anggota</span>
            </a>

            <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.peminjaman.*') }}">
                <i class="fa-solid fa-right-left w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Peminjaman</span>
            </a>

            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-5 mb-2">Sistem Pendukung Keputusan</p>

            <a href="{{ route('admin.kriteria.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.kriteria.*') }}">
                <i class="fa-solid fa-sliders w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola SPK (SAW)</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.laporan.*') }}">
                <i class="fa-solid fa-chart-column w-5 text-center"></i>
                <span class="text-sm font-medium">Laporan</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-slate-700 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nama ?? 'Admin' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->username ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-400" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"></div>

    <!-- KONTEN -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="btnToggleSidebar" class="lg:hidden text-slate-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard Admin')</h1>
                    <p class="text-xs text-slate-500">@yield('page-subtitle', 'Ringkasan aktivitas perpustakaan')</p>
                </div>
            </div>
            @yield('header-action')
        </header>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- Notifikasi sukses/gagal (flash message) --}}
            @if (session('success'))
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-50 text-rose-700 border border-rose-200 rounded-lg px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btnToggle = document.getElementById('btnToggleSidebar');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
    btnToggle?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', toggleSidebar);
</script>

@yield('scripts')
</body>
</html>
