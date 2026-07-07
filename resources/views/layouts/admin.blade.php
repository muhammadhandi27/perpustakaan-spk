<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard Admin') - Pustaka SPK</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    ink: { DEFAULT: '#16213E', soft: '#243756', light: '#3A4E76' },
                    parchment: '#FAF6ED',
                    paper: '#FFFDF8',
                    forest: { DEFAULT: '#2F6F4F', dark: '#24573F', light: '#E8F1EC' },
                    brass: { DEFAULT: '#C9A227', light: '#F3E9C6', dark: '#8F7112' },
                },
                fontFamily: {
                    display: ['Fraunces', 'serif'],
                    sans: ['Inter', 'sans-serif'],
                    mono: ['IBM Plex Mono', 'monospace'],
                },
            }
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

<style>
    /* Kartu bergaya "katalog kartu" perpustakaan klasik: takik di sudut kiri-atas
       dan garis putus-putus di sisi bawah, seperti kartu indeks lama. */
    .logo-book{
        width:34px;
        height:34px;
        display:flex;
        justify-content:center;
        align-items:center;
        font-size:26px;
        color:#C9A227;
    }
    .catalog-card {
        position: relative;
        background: #FFFDF8;
        border: 1px solid #E7E0CE;
        border-bottom: 2px dashed #D8CFB4;
    }
    .catalog-card::before {
        content: '';
        position: absolute;
        top: 0; left: 18px;
        width: 22px; height: 8px;
        background: var(--tab-color, #C9A227);
        border-radius: 0 0 3px 3px;
    }
</style>
</head>
<body class="bg-parchment font-sans">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="w-64 bg-ink text-slate-300 flex flex-col fixed lg:static inset-y-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

        <!-- Logo baru: pita bookmark bersudut, bukan ikon buku generik -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-ink-light/40">
            {{-- <svg width="34" height="34" viewBox="0 0 34 34" fill="none">
                <path d="M7 3H27V31L17 24.5L7 31V3Z" fill="#C9A227"/>
                <path d="M7 3H27V31L17 24.5L7 31V3Z" stroke="#8F7112" stroke-width="1"/>
                <rect x="11" y="9" width="12" height="1.6" fill="#16213E"/>
                <rect x="11" y="13" width="8" height="1.6" fill="#16213E"/>
            </svg> --}}
            <div class="logo-book">
                <i class="fa-solid fa-book"></i>
            </div>
            <div>
                <p class="font-display italic text-lg text-white leading-tight">SIPER-SAW</p>
                <p class="text-[10px] font-mono uppercase tracking-widest text-brass">Panel Admin</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p class="px-3 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-widest mb-2">Menu Utama</p>

            @php
                $isActive = fn ($routeName) => request()->routeIs($routeName)
                    ? 'bg-brass/15 text-brass border-l-2 border-brass'
                    : 'hover:bg-ink-soft border-l-2 border-transparent text-slate-300';
            @endphp

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.dashboard') }}">
                <i class="fa-solid fa-chart-simple w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.buku.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.buku.*') }}">
                <i class="fa-solid fa-book-open-reader w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Buku</span>
            </a>

            <a href="{{ route('admin.anggota.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.anggota.*') }}">
                <i class="fa-solid fa-id-card-clip w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Anggota</span>
            </a>

            <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.peminjaman.*') }}">
                <i class="fa-solid fa-arrows-rotate w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola Peminjaman</span>
            </a>

            <p class="px-3 text-[10px] font-mono font-semibold text-slate-500 uppercase tracking-widest mt-6 mb-2">Sistem Pendukung Keputusan</p>

            <a href="{{ route('admin.kriteria.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.kriteria.*') }}">
                <i class="fa-solid fa-scale-balanced w-5 text-center"></i>
                <span class="text-sm font-medium">Kelola SPK (SAW)</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-r-lg transition {{ $isActive('admin.laporan.*') }}">
                <i class="fa-solid fa-file-export w-5 text-center"></i>
                <span class="text-sm font-medium">Laporan</span>
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-ink-light/40 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-brass flex items-center justify-center text-sm font-display font-semibold text-ink">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nama ?? 'Admin' }}</p>
                <p class="text-xs text-slate-400 truncate font-mono">{{ '@' . (auth()->user()->username ?? '') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-brass" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"></div>

    <!-- KONTEN -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-paper border-b border-[#E7E0CE] px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="btnToggleSidebar" class="lg:hidden text-ink">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="text-lg font-display font-semibold text-ink">@yield('page-title', 'Dashboard Admin')</h1>
                    <p class="text-xs text-slate-500">@yield('page-subtitle', 'Ringkasan aktivitas perpustakaan')</p>
                </div>
            </div>
            @yield('header-action')
        </header>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            @if (session('success'))
                <div class="bg-forest-light text-forest-dark border border-forest/30 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
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
