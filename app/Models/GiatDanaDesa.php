<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiatDanaDesa extends Model
{
    use SoftDeletes;

    protected $table = 'giat_dana_desa';

    protected $fillable = ['id_kel', 'giat', 'keterangan', 'foto', 'biaya'];

    public function Kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel');
    }
}
