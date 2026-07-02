@extends('layouts.member')

@section('title', 'Rekomendasi Buku')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-2xl p-6 md:p-8 text-white">
    <h1 class="text-xl md:text-2xl font-bold">Halo, {{ auth()->user()->nama }} 👋</h1>
    <p class="text-indigo-100 text-sm mt-1">Berikut rekomendasi buku terbaik untukmu, dihitung otomatis menggunakan metode SAW.</p>
</div>

<section>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-ranking-star text-amber-500"></i>
                Hasil Ranking (SAW) — Rekomendasi Buku
            </h2>
            <p class="text-sm text-slate-500">Diurutkan berdasarkan nilai preferensi (Vi) tertinggi</p>
        </div>
        <span id="lastUpdated" class="text-xs text-slate-400"></span>
    </div>

    <div id="rekomendasiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"></div>

    <div id="emptyState" class="hidden text-center py-12 text-slate-400">
        <i class="fa-solid fa-book-open text-4xl mb-3"></i>
        <p>Belum ada data rekomendasi.</p>
    </div>
</section>

<div id="modalPinjam" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl">
        <h3 class="font-bold text-slate-800 text-lg mb-2">Konfirmasi Peminjaman</h3>
        <p class="text-sm text-slate-500 mb-5">Anda akan meminjam buku "<span id="modalJudulBuku" class="font-medium text-slate-700"></span>". Lanjutkan?</p>
        <div class="flex gap-3">
            <button onclick="tutupModal()" class="flex-1 border border-slate-200 text-slate-600 rounded-lg py-2 text-sm font-medium hover:bg-slate-50">Batal</button>
            <button onclick="konfirmasiPinjam()" class="flex-1 bg-indigo-600 text-white rounded-lg py-2 text-sm font-medium hover:bg-indigo-700">Ya, Pinjam</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const WARNA_KATEGORI = {
        "Programming": "bg-blue-100 text-blue-700",
        "Artificial Intelligent": "bg-purple-100 text-purple-700",
        "Sains": "bg-teal-100 text-teal-700",
        "Self-Improvement": "bg-amber-100 text-amber-700",
        "Sosial": "bg-rose-100 text-rose-700",
        "Fiksi": "bg-indigo-100 text-indigo-700",
    };
    const BADGE_RANKING = {
        1: { icon: "🥇", warna: "bg-amber-400" },
        2: { icon: "🥈", warna: "bg-slate-300" },
        3: { icon: "🥉", warna: "bg-orange-300" },
    };

    let idBukuDipilih = null;
    let judulBukuDipilih = null;

    // Ambil data rekomendasi dari endpoint Laravel yang sudah dilindungi auth
    async function ambilDataRekomendasi() {
        try {
            const response = await fetch("{{ route('rekomendasi.tampilkan') }}");
            if (!response.ok) throw new Error('Gagal mengambil data');
            const result = await response.json();
            renderRekomendasi(result.data);
        } catch (error) {
            console.error('Gagal memuat rekomendasi:', error);
            document.getElementById('emptyState').classList.remove('hidden');
        }
    }

    function renderRekomendasi(daftarBuku) {
        const grid = document.getElementById('rekomendasiGrid');
        const emptyState = document.getElementById('emptyState');
        grid.innerHTML = '';

        if (!daftarBuku || daftarBuku.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }
        emptyState.classList.add('hidden');

        daftarBuku.forEach((buku) => grid.insertAdjacentHTML('beforeend', buatKartuBuku(buku)));
        document.getElementById('lastUpdated').textContent = 'Diperbarui: ' + new Date().toLocaleString('id-ID');
    }

    function buatKartuBuku(buku) {
        const warnaKategori = WARNA_KATEGORI[buku.kategori] || 'bg-slate-100 text-slate-600';
        const topBadge = BADGE_RANKING[buku.ranking];
        const persenSkor = Math.min(Math.round(buku.bobot_preferensi * 100), 100);

        return `
        <div class="relative bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition p-5 flex flex-col">
            <div class="absolute -top-3 -left-3 w-9 h-9 rounded-full ${topBadge ? topBadge.warna : 'bg-slate-700'} text-white flex items-center justify-center text-sm font-bold shadow">
                ${topBadge ? topBadge.icon : '#' + buku.ranking}
            </div>
            <div class="flex justify-end mb-3">
                <span class="text-xs font-medium px-2.5 py-1 rounded-full ${warnaKategori}">${buku.kategori}</span>
            </div>
            <div class="w-full h-32 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center mb-4">
                <i class="fa-solid fa-book text-white text-3xl opacity-80"></i>
            </div>
            <h3 class="font-bold text-slate-800 leading-snug line-clamp-2">${buku.judul}</h3>
            <p class="text-sm text-slate-500 mb-3">oleh ${buku.penulis}</p>
            <div class="mb-4">
                <div class="flex justify-between text-xs text-slate-500 mb-1">
                    <span>Nilai Preferensi (Vi)</span>
                    <span class="font-semibold text-indigo-600">${Number(buku.bobot_preferensi).toFixed(4)}</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full" style="width: ${persenSkor}%"></div>
                </div>
            </div>
            <button onclick='bukaModalPinjam(${buku.id_buku}, ${JSON.stringify(buku.judul)})'
                class="mt-auto w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-hand-holding-hand"></i> Pinjam
            </button>
        </div>`;
    }

    function bukaModalPinjam(idBuku, judulBuku) {
        idBukuDipilih = idBuku;
        judulBukuDipilih = judulBuku;
        document.getElementById('modalJudulBuku').textContent = judulBuku;
        const modal = document.getElementById('modalPinjam');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModal() {
        const modal = document.getElementById('modalPinjam');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Kirim permintaan pinjam ke backend (Laravel) menggunakan CSRF token
    async function konfirmasiPinjam() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        try {
            const response = await fetch(`/anggota/pinjam/${idBukuDipilih}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            const result = await response.json();

            if (response.ok) {
                alert('Buku "' + judulBukuDipilih + '" berhasil dipinjam!');
                ambilDataRekomendasi(); // refresh data
            } else {
                alert(result.message || 'Gagal meminjam buku.');
            }
        } catch (error) {
            alert('Terjadi kesalahan jaringan.');
        }
        tutupModal();
    }

    document.addEventListener('DOMContentLoaded', ambilDataRekomendasi);
</script>
@endsection
