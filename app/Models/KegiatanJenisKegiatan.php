<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanJenisKegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_jenis_kegiatan';

    protected $guarded = [];

    public function kegiatan() {
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan');
    }
    
    public function jenis_kegiatan() {
        return $this->belongsTo(JenisKegiatan::class, 'id_jenis_kegiatan');
    }
}
