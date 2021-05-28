<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory, UserTimezoneAware;

    protected $table = 'pers_jabatan';
    protected $fillable = ['id_kesatuan', 'jabatan', 'status_pimpinan'];

    public function dinas(){
        return $this->belongsTo(Dinas::class, 'id_dinas');
    }
}
