<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    protected $guarded = [];

    public function penilaian()
    {
        return $this->hasMany(PenilaianBuku::class, 'id_buku', 'id_buku');
    }

    public function rekomendasi()
    {
        return $this->hasMany(RekomendasiBuku::class, 'id_buku', 'id_buku');
    }
}
