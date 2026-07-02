<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianBuku extends Model
{
    protected $table = 'penilaian_buku';
    protected $primaryKey = null;  // composite key (id_buku, id_kriteria)
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria', 'id_kriteria');
    }
}
