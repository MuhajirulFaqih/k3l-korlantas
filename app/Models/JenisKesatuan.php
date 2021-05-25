<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKesatuan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kesatuan';

    public function kesatuan()
    {
        return $this->hasMany(Kesatuan::class, 'id_jenis_kesatuan');
    }
}
