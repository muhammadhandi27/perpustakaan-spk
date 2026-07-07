@extends('layouts.member')

@section('title', 'Rekomendasi Buku')

@section('content')

<div class="bg-ink rounded-2xl p-6 md:p-8 text-white relative overflow-hidden">
    <div class="absolute -right-6 -bottom-8 opacity-10">
        <i class="fa-solid fa-book-open text-[140px]"></i>
    </div>
    <h1 class="text-xl md:text-2xl font-display font-semibold relative">Selamat datang, {{ auth()->user()->nama }}</h1>
    <p class="text-slate-300 text-sm mt-1 relative max-w-lg">Rak rekomendasi berikut disusun otomatis oleh Sistem Pendukung Keputusan (metode SAW) berdasarkan rating, tahun terbit, popularitas, dan kategori buku.</p>
</div>

<section>
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-display font-semibold text-ink flex items-center gap-2">
                <i class="fa-solid fa-ranking-star text-brass"></i>
                Rak Rekomendasi (Hasil SAW)
            </h2>
            <p class="text-sm text-slate-500">Diurutkan berdasarkan nilai preferensi (Vi) tertinggi</p>
        </div>
        <span id="lastUpdated" class="text-xs text-slate-400 font-mono hidden sm:block"></span>
    </div>

    {{-- Grid Standar: lebar kolom tetap, tinggi seragam --}}
    <div id="rekomendasiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 items-stretch gap-5"></div>

    <div id="emptyState" class="hidden text-center py-16 text-slate-400">
        <i class="fa-solid fa-box-open text-4xl mb-3"></i>
        <p>Belum ada data rekomendasi.</p>
    </div>
</section>

<div id="modalPinjam" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-paper rounded-xl p-6 w-full max-w-sm shadow-xl border border-[#E7E0CE]">
        <h3 class="font-display font-semibold text-ink text-lg mb-2">Konfirmasi Peminjaman</h3>
        <p class="text-sm text-slate-500 mb-5">Anda akan meminjam buku "<span id="modalJudulBuku" class="font-medium text-ink"></span>". Lanjutkan?</p>
        <div class="flex gap-3">
            <button onclick="tutupModal()" class="flex-1 border border-slate-200 text-slate-600 rounded-lg py-2 text-sm font-medium hover:bg-slate-50">Batal</button>
            <button onclick="konfirmasiPinjam()" class="flex-1 bg-forest text-white rounded-lg py-2 text-sm font-medium hover:bg-forest-dark">Ya, Pinjam</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ---------------------------------------------------------------
    // PALET & METADATA KATEGORI — dipakai untuk mewarnai cover buku
    // seperti jilid kulit di rak perpustakaan klasik.
    // ---------------------------------------------------------------
    const KATEGORI_META = {
        "Programming":            { warna: ['#3A8563', '#1F4A35'], kode: 'PRG' },
        "Artificial Intelligent": { warna: ['#5578C2', '#28407D'], kode: 'AI'  },
        "Sains":                  { warna: ['#28948F', '#125D59'], kode: 'SNS' },
        "Self-Improvement":       { warna: ['#DDB94A', '#8F7112'], kode: 'SIM' },
        "Sosial":                 { warna: ['#9A5A5A', '#5C2A2A'], kode: 'SOS' },
        "Fiksi":                  { warna: ['#7566AD', '#43356E'], kode: 'FIK' },
    };

    // Tinggi cover dibuat SERAGAM untuk semua buku, agar kartu rapi
    // sejajar dan urutan render tetap kiri-ke-kanan / atas-ke-bawah.
    const TINGGI_COVER = 220;

    let idBukuDipilih = null;
    let judulBukuDipilih = null;

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

        const el = document.getElementById('lastUpdated');
        el.textContent = 'Diperbarui ' + new Date().toLocaleString('id-ID');
        el.classList.remove('hidden');
    }

    // Membuat markup bintang rating (skala 0-5, pembulatan ke 0.5 terdekat)
    function buatBintang(rating) {
        const penuh = Math.floor(rating);
        const setengah = (rating - penuh) >= 0.5;
        let html = '';
        for (let i = 0; i < 5; i++) {
            if (i < penuh) html += '<i class="fa-solid fa-star text-brass text-[11px]"></i>';
            else if (i === penuh && setengah) html += '<i class="fa-solid fa-star-half-stroke text-brass text-[11px]"></i>';
            else html += '<i class="fa-regular fa-star text-slate-300 text-[11px]"></i>';
        }
        return html;
    }

    // Pita ranking untuk 3 besar (emas/perak/perunggu) — bentuk pita, bukan emoji
    function buatPitaRanking(ranking) {
        const gaya = {
            1: { bg: '#C9A227', teks: '#3A2E06' },
            2: { bg: '#B9C0C9', teks: '#33383F' },
            3: { bg: '#B87A4A', teks: '#3A2410' },
        };
        if (!gaya[ranking]) {
            return `<div class="absolute top-3 right-3 bg-ink/80 text-white text-[10px] font-mono font-semibold px-2 py-1 rounded">#${ranking}</div>`;
        }
        const g = gaya[ranking];
        return `
        <div class="absolute -top-1 right-4 flex flex-col items-center drop-shadow-md">
            <div class="w-8 h-10 flex items-center justify-center text-xs font-mono font-bold rounded-sm"
                 style="background:${g.bg}; color:${g.teks}; clip-path: polygon(0 0, 100% 0, 100% 82%, 50% 100%, 0 82%)">
                ${ranking}
            </div>
        </div>`;
    }

    function buatKartuBuku(buku) {
        const meta = KATEGORI_META[buku.kategori] || { warna: ['#5A6472', '#33383F'], kode: 'GEN' };
        
        // Perbaikan: Baris PROFIL_TINGGI yang error telah dihapus karena tinggi sudah seragam
        const kodeRak = `${meta.kode}-${String(buku.id_buku).padStart(3, '0')}`;
        
        // Amankan nilai bobot agar tidak meledak saat toFixed jika bernilai null/undefined
        const bobot = buku.bobot_preferensi ? parseFloat(buku.bobot_preferensi) : 0;
        const persenSkor = Math.min(Math.round(bobot * 100), 100);
        const stokHabis = (buku.stok ?? 1) < 1;

        return `
            <div class="h-full flex flex-col bg-paper rounded-xl border border-[#E7E0CE] shadow-sm hover:shadow-md transition overflow-hidden">

                <!-- COVER: gradien warna kategori + tekstur tepi halaman -->
                <div class="relative flex flex-col justify-between p-4 shrink-0"
                     style="height:${TINGGI_COVER}px; background: linear-gradient(150deg, ${meta.warna[0]}, ${meta.warna[1]});">

                    <!-- Tekstur tepi halaman di sisi kanan -->
                    <div class="absolute top-0 right-0 h-full w-2 opacity-40"
                         style="background: repeating-linear-gradient(180deg, #fff 0 2px, transparent 2px 4px);"></div>

                    ${buatPitaRanking(buku.ranking)}

                    <div class="flex items-center justify-between relative">
                        <span class="text-[10px] font-mono tracking-widest text-white/70 uppercase">${buku.kategori}</span>
                    </div>

                    <div class="relative">
                        <h3 class="font-display text-white text-lg leading-snug line-clamp-3" style="text-shadow: 0 1px 3px rgba(0,0,0,0.35)">
                            ${buku.judul}
                        </h3>
                        <div class="w-8 h-[2px] bg-white/50 my-2"></div>
                        <p class="text-white/80 text-xs">oleh ${buku.penulis}</p>
                    </div>
                </div>

                <!-- LABEL RAK (call number) -->
                <div class="px-4 py-2 bg-ink/5 border-b border-[#E7E0CE] flex items-center justify-between shrink-0">
                    <span class="font-mono text-[11px] text-slate-500 tracking-wide">${kodeRak}</span>
                    <div class="flex gap-0.5">${buatBintang(buku.rating || 0)}</div>
                </div>

                <!-- INFO & AKSI -->
                <div class="p-4 flex-col flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-slate-500">Nilai Preferensi (Vi)</span>
                        <span class="font-mono text-sm font-semibold text-ink">${bobot.toFixed(4)}</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-3">
                        <div class="h-full bg-brass rounded-full" style="width: ${persenSkor}%"></div>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] px-2 py-0.5 rounded-full ${stokHabis ? 'bg-rose-50 text-rose-600' : 'bg-forest-light text-forest-dark'}">
                            <i class="fa-solid ${stokHabis ? 'fa-ban' : 'fa-check'} mr-1"></i>
                            ${stokHabis ? 'Stok habis' : 'Tersedia · ' + (buku.stok ?? 0)}
                        </span>
                    </div>

                    <button ${stokHabis ? 'disabled' : `onclick='bukaModalPinjam(${buku.id_buku}, ${JSON.stringify(buku.judul)})'`}
                        class="w-full ${stokHabis ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-forest hover:bg-forest-dark text-white'} text-sm font-medium py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-hand-holding-hand"></i> ${stokHabis ? 'Tidak Tersedia' : 'Pinjam'}
                    </button>
                </div>
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

    async function konfirmasiPinjam() {
        const tokenEl = document.querySelector('meta[name="csrf-token"]');
        if (!tokenEl) {
            alert('Tag meta CSRF Token tidak ditemukan pada layout!');
            return;
        }
        const csrfToken = tokenEl.content;
        
        try {
            const response = await fetch(`/anggota/pinjam/${idBukuDipilih}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            const result = await response.json();
            if (response.ok) {
                alert('Buku "' + judulBukuDipilih + '" berhasil dipinjam!');
                ambilDataRekomendasi();
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