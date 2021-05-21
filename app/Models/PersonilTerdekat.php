<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonilTerdekat extends Model
{
    use HasFactory;
    protected $table = 'personil_terdekat';

    protected $fillable = ['id_personil', 'id_induk', 'jenis_induk', 'lat', 'lng'];

    public function induk(){
        return $this->morphTo(null, 'jenis_induk', 'id_induk');
    }

    public function personil(){
        return $this->belongsTo(Personil::class, 'id_personil');
    }

}
