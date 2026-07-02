<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiBuku extends Model
{
    protected $table = 'rekomendasi_buku';
    protected $primaryKey = 'id_hasil';
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Cast eksplisit diperlukan karena kolom DECIMAL di MySQL
     * secara default dikembalikan Eloquent sebagai STRING (bukan number).
     * Tanpa ini, response JSON akan mengirim "0.9421" (string),
     * yang menyebabkan method JS seperti .toFixed() gagal karena
     * .toFixed() hanya ada pada tipe Number, bukan String.
     */
    protected $casts = [
        'bobot_preferensi' => 'float',
        'ranking'          => 'integer',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
