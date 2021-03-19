<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasyarakatKegiatanBhabin extends Model
{
    protected $table = 'masyarakat_kegiatan_bhabin';
    protected $guarded = [];

    public function agama()
    {
    	return $this->belongsTo(Agama::class, 'id_agama');
    }
}
