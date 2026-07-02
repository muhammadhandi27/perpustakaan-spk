<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login.
     * Setelah berhasil, arahkan ke dashboard sesuai role (admin/anggota).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return $user->isAdmin()
                ? redirect()->intended(route('admin.dashboard'))
                : redirect()->intended(route('anggota.dashboard'));
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    /**
     * Menampilkan form pendaftaran anggota baru.
     * (Pendaftaran mandiri selalu berperan sebagai 'anggota', bukan admin)
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran anggota baru.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50', 'unique:anggota,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nama'     => ['required', 'string', 'max:100'],
            'alamat'   => ['nullable', 'string'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $anggota = \App\Models\Anggota::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama'     => $request->nama,
            'alamat'   => $request->alamat,
            'no_hp'    => $request->no_hp,
            'role'     => 'anggota',
        ]);

        Auth::login($anggota);

        return redirect()->route('anggota.dashboard');
    }

    /**
     * Logout & hancurkan sesi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
