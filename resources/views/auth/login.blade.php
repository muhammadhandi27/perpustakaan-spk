<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Perpustakaan SPK</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-sm">
    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-book text-white text-lg"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-800">SIPER-SAW</h1>
        <p class="text-sm text-slate-500">Masuk untuk melanjutkan</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">

        @if ($errors->any())
            <div class="bg-rose-50 text-rose-700 border border-rose-200 rounded-lg px-4 py-3 text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan username">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan password">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Ingat saya
            </label>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-5">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">Daftar di sini</a>
        </p>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
        Demo akun — Admin: <code>admin / admin123</code> &nbsp;|&nbsp; Anggota: <code>handi / handi123</code>
    </p>
</div>

</body>
</html>
