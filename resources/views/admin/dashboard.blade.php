@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan koleksi, anggota, dan hasil SPK per ' . now()->translatedFormat('d F Y'))

@section('header-action')
    <a href="{{ route('admin.spk.hitung') }}" target="_blank"
       class="bg-forest hover:bg-forest-dark text-white text-sm font-medium px-4 py-2.5 rounded-lg transition flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-calculator"></i> Jalankan Perhitungan SAW
    </a>
@endsection

@section('content')

@php
    $namaBulanSingkat = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // --- Statistik ringkas ---
    $totalBuku = \App\Models\Buku::count();
    $stokMenipis = \App\Models\Buku::where('stok', '<=', 2)->count();
    $totalAnggota = \App\Models\Anggota::where('role', 'anggota')->count();

    $bulanIni = now();
    $bulanLalu = now()->subMonthNoOverflow();
    $peminjamanBulanIni = \App\Models\Peminjaman::whereMonth('tanggal_pinjam', $bulanIni->month)->whereYear('tanggal_pinjam', $bulanIni->year)->count();
    $peminjamanBulanLalu = \App\Models\Peminjaman::whereMonth('tanggal_pinjam', $bulanLalu->month)->whereYear('tanggal_pinjam', $bulanLalu->year)->count();
    $deltaPeminjaman = $peminjamanBulanLalu > 0
        ? round((($peminjamanBulanIni - $peminjamanBulanLalu) / $peminjamanBulanLalu) * 100)
        : ($peminjamanBulanIni > 0 ? 100 : 0);
    $peminjamanAktif = \App\Models\Peminjaman::where('status', 'Dipinjam')->count();
    $peminjamanTerlambat = \App\Models\Peminjaman::where('status', 'Terlambat')->count();

    $topRekomendasi = \App\Models\RekomendasiBuku::with('buku')->orderBy('ranking')->take(5)->get();
    $skorTertinggi = $topRekomendasi->first()->bobot_preferensi ?? 0;

    // --- Data grafik: tren peminjaman 6 bulan terakhir ---
    $labelBulan = [];
    $dataBulan = [];
    for ($i = 5; $i >= 0; $i--) {
        $t = now()->subMonthsNoOverflow($i);
        $labelBulan[] = $namaBulanSingkat[$t->month];
        $dataBulan[] = \App\Models\Peminjaman::whereMonth('tanggal_pinjam', $t->month)->whereYear('tanggal_pinjam', $t->year)->count();
    }

    // --- Data grafik: distribusi buku per kategori ---
    $kategoriUrut = ['Programming', 'Artificial Intelligent', 'Sains', 'Self-Improvement', 'Sosial', 'Fiksi'];
    $kategoriCount = \App\Models\Buku::selectRaw('kategori, count(*) as jumlah')->groupBy('kategori')->pluck('jumlah', 'kategori');
    $dataKategori = collect($kategoriUrut)->map(fn ($k) => $kategoriCount[$k] ?? 0);
@endphp

{{-- ============================================================
     BARIS 1: KARTU STATISTIK BERGAYA "KARTU KATALOG"
============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="catalog-card rounded-lg p-5" style="--tab-color:#2F6F4F">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-forest-light text-forest rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Koleksi</span>
        </div>
        <p class="text-3xl font-display font-semibold text-ink">{{ $totalBuku }}</p>
        <p class="text-sm text-slate-500 mt-1">Total judul buku terdaftar</p>
        @if ($stokMenipis > 0)
            <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $stokMenipis }} judul stoknya menipis (≤2)
            </p>
        @endif
    </div>

    <div class="catalog-card rounded-lg p-5" style="--tab-color:#C9A227">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-brass-light text-brass-dark rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Anggota</span>
        </div>
        <p class="text-3xl font-display font-semibold text-ink">{{ $totalAnggota }}</p>
        <p class="text-sm text-slate-500 mt-1">Anggota terdaftar aktif</p>
    </div>

    <div class="catalog-card rounded-lg p-5" style="--tab-color:#3B5BA5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 text-[#3B5BA5] rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Bulan Ini</span>
        </div>

        <p class="text-3xl font-display font-semibold text-ink">{{ $peminjamanBulanIni }}</p>
        
        <div class="flex-col justify-between">
            <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
            Jumlah peminjaman
            </p>
            <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                <span class="{{ $deltaPeminjaman >= 0 ? 'text-forest' : 'text-rose-500' }} font-medium flex items-center gap-0.5">
                    <i class="fa-solid {{ $deltaPeminjaman >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                    {{ abs($deltaPeminjaman) }}%
                </span>
                dari bulan lalu ({{ $peminjamanBulanLalu }})
            </p>
        </div>
    </div>

    <div class="catalog-card rounded-lg p-5" style="--tab-color:#7B3F3F">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-rose-50 text-[#7B3F3F] rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Berjalan</span>
        </div>
        <p class="text-3xl font-display font-semibold text-ink">{{ $peminjamanAktif }}</p>
        <p class="text-sm text-slate-500 mt-1">
            Sedang dipinjam
            @if ($peminjamanTerlambat > 0)
                <span class="text-rose-500 font-medium">· {{ $peminjamanTerlambat }} terlambat</span>
            @endif
        </p>
    </div>
</div>

{{-- ============================================================
     BARIS 2: GRAFIK
============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

    <div class="lg:col-span-3 bg-paper rounded-xl border border-[#E7E0CE] p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-display font-semibold text-ink">Tren Peminjaman 6 Bulan Terakhir</h2>
            <i class="fa-solid fa-chart-column text-slate-300"></i>
        </div>
        <p class="text-xs text-slate-500 mb-4">Jumlah transaksi peminjaman per bulan, {{ $labelBulan[0] }}–{{ $labelBulan[5] }} {{ now()->year }}</p>
        <canvas id="chartTren" height="140"></canvas>
    </div>

    <div class="lg:col-span-2 bg-paper rounded-xl border border-[#E7E0CE] p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-display font-semibold text-ink">Distribusi Kategori</h2>
            <i class="fa-solid fa-chart-pie text-slate-300"></i>
        </div>
        <p class="text-xs text-slate-500 mb-4">Komposisi koleksi buku berdasar kategori (kriteria C4)</p>
        <canvas id="chartKategori" height="200"></canvas>
    </div>
</div>

{{-- ============================================================
     BARIS 3: TOP REKOMENDASI SAW
============================================================ --}}
<div class="bg-paper rounded-xl border border-[#E7E0CE] overflow-hidden">
    <div class="px-6 py-4 border-b border-[#E7E0CE] flex items-center justify-between">
        <div>
            <h2 class="font-display font-semibold text-ink">5 Buku Peringkat Teratas — Metode SAW</h2>
            <p class="text-xs text-slate-500">Bobot kriteria: C1 Rating=0.30 · C2 Tahun=0.25 · C3 Peminjaman=0.25 · C4 Kategori=0.20</p>
        </div>
        <a href="{{ route('admin.laporan.index') }}" class="text-sm text-forest font-medium hover:underline">Lihat Laporan Lengkap →</a>
    </div>

    <div class="divide-y divide-[#EFE9D8]">
        @forelse ($topRekomendasi as $item)
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-9 h-9 rounded-full bg-ink text-brass font-mono font-semibold text-sm flex items-center justify-center shrink-0">
                    {{ $item->ranking }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-ink truncate">{{ $item->buku->judul ?? '-' }}</p>
                    <p class="text-xs text-slate-500">{{ $item->buku->kategori ?? '-' }} · {{ $item->buku->penulis ?? '-' }}</p>
                </div>
                <div class="w-32 hidden sm:block">
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-brass rounded-full" style="width: {{ $skorTertinggi > 0 ? round(($item->bobot_preferensi / $skorTertinggi) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <span class="font-mono text-sm font-semibold text-ink w-16 text-right">{{ number_format($item->bobot_preferensi, 4) }}</span>
            </div>
        @empty
            <div class="px-6 py-10 text-center text-slate-400">
                <i class="fa-solid fa-calculator text-3xl mb-2"></i>
                <p>Belum ada hasil. Klik "Jalankan Perhitungan SAW" di atas.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
<script>
    // Palet warna senada dengan identitas "Katalog Perpustakaan Klasik"
    const WARNA_INK = '#16213E';
    const WARNA_FOREST = '#2F6F4F';
    const WARNA_BRASS = '#C9A227';

    // Grafik 1: Tren peminjaman per bulan (bar chart)
    new Chart(document.getElementById('chartTren'), {
        type: 'bar',
        data: {
            labels: @json($labelBulan),
            datasets: [{
                label: 'Peminjaman',
                data: @json($dataBulan),
                backgroundColor: WARNA_FOREST,
                borderRadius: 6,
                maxBarThickness: 36,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#EFE9D8' } },
                x: { grid: { display: false } },
            }
        }
    });

    // Grafik 2: Distribusi kategori (doughnut chart)
    new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        data: {
            labels: @json($kategoriUrut),
            datasets: [{
                data: @json($dataKategori),
                backgroundColor: ['#2F6F4F', '#3B5BA5', '#1F7A76', '#C9A227', '#7B3F3F', '#5B4B8A'],
                borderColor: '#FFFDF8',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10, padding: 12 } }
            }
        }
    });
</script>
@endsection
