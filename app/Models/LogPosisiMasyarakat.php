<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Model;

class LogPosisiMasyarakat extends Model
{
    use UserTimezoneAware;

    protected $table = 'log_masyarakat';

    protected $fillable = ['id_user', 'id_induk', 'jenis_induk', 'lat', 'lng'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function induk(){
        return $this->morphTo(null, 'jenis_induk', 'id_induk');
    }
}
