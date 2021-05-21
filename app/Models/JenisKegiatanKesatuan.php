<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatanKesatuan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kegiatan_kesatuan';

    protected $guarded = [];

    public function jenis_kegiatan() {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan');
    }
}
