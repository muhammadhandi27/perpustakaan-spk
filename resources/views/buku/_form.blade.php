{{-- Partial form input data buku — dipakai ulang oleh buku/create.blade.php dan buku/edit.blade.php --}}
@php
    $kategoriList = ['Programming', 'Artificial Intelligent', 'Sains', 'Self-Improvement', 'Sosial', 'Fiksi'];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Buku</label>
        <input type="text" name="judul" value="{{ old('judul', $buku->judul ?? '') }}" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Penulis</label>
        <input type="text" name="penulis" value="{{ old('penulis', $buku->penulis ?? '') }}" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Penerbit</label>
        <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit ?? '') }}" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit ?? date('Y')) }}" required min="1900" max="{{ date('Y') }}"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Kategori
            <span class="text-xs text-slate-400 font-normal">(menentukan nilai kriteria C4)</span>
        </label>
        <select name="kategori" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach ($kategoriList as $kategori)
                <option value="{{ $kategori }}" {{ old('kategori', $buku->kategori ?? '') === $kategori ? 'selected' : '' }}>
                    {{ $kategori }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Stok</label>
        <input type="number" name="stok" value="{{ old('stok', $buku->stok ?? 0) }}" required min="0"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Rating <span class="text-xs text-slate-400 font-normal">(kriteria C1, skala 0-5)</span>
        </label>
        <input type="number" step="0.01" name="rating" value="{{ old('rating', $buku->rating ?? 0) }}" min="0" max="5"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Jumlah Peminjaman Awal <span class="text-xs text-slate-400 font-normal">(kriteria C3)</span>
        </label>
        <input type="number" name="jumlah_pinjam" value="{{ old('jumlah_pinjam', $buku->jumlah_pinjam ?? 0) }}" min="0"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
</div>
