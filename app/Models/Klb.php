<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Klb extends Model
{
    use HasFactory, FilterJenisPemilik, UserTimezoneAware;
    protected $table = 'klb';
    protected $fillable = ['id_user', 'lat', 'lng', 'id_kesatuan', 'keterangan'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }

}
