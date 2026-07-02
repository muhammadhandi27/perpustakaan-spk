<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder ini membuat 1 akun Admin default agar Anda bisa langsung login
 * tanpa perlu mendaftar manual.
 *
 * Jalankan dengan: php artisan db:seed --class=AdminSeeder
 * (atau daftarkan di DatabaseSeeder.php agar otomatis ikut `php artisan migrate --seed`)
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Anggota::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'), // ganti setelah demo!
                'nama'     => 'Administrator',
                'alamat'   => '-',
                'no_hp'    => '-',
                'role'     => 'admin',
            ]
        );

        Anggota::updateOrCreate(
            ['username' => 'rina'],
            [
                'password' => Hash::make('anggota123'),
                'nama'     => 'Rina Kurnia',
                'alamat'   => 'Jl. Contoh No. 1',
                'no_hp'    => '081234567890',
                'role'     => 'anggota',
            ]
        );
    }
}
