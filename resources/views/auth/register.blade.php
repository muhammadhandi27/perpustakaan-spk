<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun - Perpustakaan SPK</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex items-center justify-center px-4 py-10">

<div class="w-full max-w-sm">
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-user-plus text-white text-lg"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-800">Daftar Akun Anggota</h1>
        <p class="text-sm text-slate-500">Buat akun untuk mulai meminjam buku</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">

        @if ($errors->any())
            <div class="bg-rose-50 text-rose-700 border border-rose-200 rounded-lg px-4 py-3 text-sm mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('alamat') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition">
                Daftar
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>

</body>
</html>
