<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Kesatuan extends Model
{
    use HasFactory, NodeTrait;

    protected $table = 'pers_kesatuan';

    protected $fillable = ['kesatuan', 'id_jenis', 'level', 'kode_satuan'];

    public function auth(){
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function personil(){
        return $this->hasMany(Personil::class, 'id_kesatuan');
    }

    public function htchannel(){
        return $this->hasMany(KesatuanHTChannel::class, 'id_kesatuan');
    }
}
