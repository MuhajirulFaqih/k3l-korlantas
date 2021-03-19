<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paslon extends Model
{
    protected $table = 'paslon';

    protected $fillable = ['nama_ka', 'id_deskel', 'nama_waka', 'foto', 'no_urut', 'color'];


    public function perolehan_suara() {
        return $this->hasMany(PerolehanSuara::class, 'id_paslon');
    }

    public function getTotalSuaraAttribute()
    {
        return $this->perolehan_suara->sum('suara');
    }
}
