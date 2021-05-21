<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatVital extends Model
{
	protected $table = 'tempat_vital';

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }

    public function jajaran()
    {
        return $this->belongsTo(Kesatuan::class, 'id_jajaran');
    }
}
