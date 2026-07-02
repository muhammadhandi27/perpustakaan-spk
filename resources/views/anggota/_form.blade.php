{{-- Partial form input data anggota — dipakai ulang oleh anggota/create.blade.php dan anggota/edit.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama" value="{{ old('nama', $anggota->nama ?? '') }}" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
        <input type="text" name="username" value="{{ old('username', $anggota->username ?? '') }}" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Password
            @isset($anggota)<span class="text-xs text-slate-400 font-normal">(kosongkan jika tidak diubah)</span>@endisset
        </label>
        <input type="password" name="password" {{ isset($anggota) ? '' : 'required' }}
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
        <select name="role" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="anggota" {{ old('role', $anggota->role ?? 'anggota') === 'anggota' ? 'selected' : '' }}>Anggota</option>
            <option value="admin" {{ old('role', $anggota->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
        <input type="text" name="no_hp" value="{{ old('no_hp', $anggota->no_hp ?? '') }}"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
        <textarea name="alamat" rows="2"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('alamat', $anggota->alamat ?? '') }}</textarea>
    </div>
</div>
