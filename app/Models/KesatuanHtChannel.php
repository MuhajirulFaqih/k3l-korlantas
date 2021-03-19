<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KesatuanHtChannel extends Model
{

    protected $table = 'kesatuan_ht_channel';

    protected $fillable = ['id_kesatuan', 'id_channel'];

    public function htchannel(){
        return $this->belongsTo(HTChannels::class, 'id_channel');
    }

    public function kesatuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }
}
