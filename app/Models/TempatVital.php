<?php

namespace App\Models;

use App\Models\Jenis;
use App\Models\Kesatuan;
use App\Models\TempatVital;
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
