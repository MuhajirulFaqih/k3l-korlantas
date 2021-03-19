<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'pers_jabatan';

    protected $casts = ['status_pimpinan' => 'boolean', 'sispammako' => 'boolean', 'aksess_dana_desa' => 'boolean'];

    public function Dinas()
    {
        return $this->belongsTo(Dinas::class,'id_dinas');
    }

    public function kesatuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }
}
