<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilKelurahan extends Model
{
    protected $table = 'profil_kelurahan';

    protected $fillable = ['id_kel', 'kades', 'satuan_luas', 'sekdes', 'luas_daerah', 'jumlah_penduduk', 'batas_utara', 'batas_selatan', 'batas_timur', 'batas_barat', 'lat', 'long', 'zoom'];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel');
    }
}
