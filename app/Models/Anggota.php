<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model Anggota digunakan sebagai model autentikasi utama (menggantikan
 * model User bawaan Laravel), karena tabel `anggota` sudah mewakili
 * "pengguna" sistem — baik yang berperan sebagai admin maupun anggota biasa,
 * dibedakan lewat kolom `role`.
 */
class Anggota extends Authenticatable
{
    use Notifiable;

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    // Tabel `anggota` hanya punya kolom created_at (tanpa updated_at),
    // jadi timestamp otomatis Eloquent harus dimatikan agar tidak error.
    public $timestamps = false;

    protected $fillable = [
        'username', 'password', 'nama', 'alamat', 'no_hp', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Cek apakah anggota ini adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relasi: satu anggota bisa memiliki banyak transaksi peminjaman.
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_anggota', 'id_anggota');
    }
}
