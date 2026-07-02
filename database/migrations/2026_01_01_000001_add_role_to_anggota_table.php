<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini MENAMBAHKAN kolom yang dibutuhkan Laravel Auth
 * ke tabel `anggota` yang sudah ada (dari database_schema.sql).
 *
 * - role            : membedakan hak akses Admin vs Anggota
 * - remember_token  : dibutuhkan fitur "Ingat Saya" saat login
 *
 * Jalankan dengan: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->enum('role', ['admin', 'anggota'])
                  ->default('anggota')
                  ->after('no_hp');

            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['role', 'remember_token']);
        });
    }
};
