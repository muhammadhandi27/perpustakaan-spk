<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard Anggota') - Pustaka SPK</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .logo-book{
        width:34px;
        height:34px;
        display:flex;
        justify-content:center;
        align-items:center;
        font-size:26px;
        color:#C9A227;
    }
</style>

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
</head>
<body class="bg-parchment font-sans">

<header class="bg-paper border-b border-[#E7E0CE] sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="logo-book">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <span class="font-display italic font-semibold text-ink text-lg">SIPER-SAW</span>
        </div>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            <a href="{{ route('anggota.dashboard') }}" class="{{ request()->routeIs('anggota.dashboard') ? 'text-forest font-semibold' : 'hover:text-forest' }}">Beranda</a>
            <a href="{{ route('anggota.riwayat') }}" class="{{ request()->routeIs('anggota.riwayat') ? 'text-forest font-semibold' : 'hover:text-forest' }}">Riwayat Pinjam</a>
        </nav>

        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-sm text-slate-600">{{ auth()->user()->nama ?? 'Anggota' }}</span>
            <div class="w-9 h-9 rounded-full bg-brass-light text-brass-dark flex items-center justify-center font-display font-semibold text-sm">
                {{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-500" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-8 space-y-8">
    @if (session('success'))
        <div class="bg-forest-light text-forest-dark border border-forest/30 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

@yield('scripts')
</body>
</html>
