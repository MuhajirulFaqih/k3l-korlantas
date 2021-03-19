<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerolehanSuara extends Model
{
    protected $table = 'perolehan_suara';

    protected $fillable = ['id_tps', 'id_paslon', 'suara'];

    public function paslon(){
        return $this->belongsTo(Paslon::class, 'id_paslon');
    }

    public function tps(){
        return $this->belongsTo(Tps::class, 'id_tps');
    }
}
