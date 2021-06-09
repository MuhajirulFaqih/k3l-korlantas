<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Plb extends Model
{
    use HasFactory, FilterJenisPemilik, UserTimezoneAware;
    protected $table = 'plb';
    protected $fillable = ['id_user', 'lat', 'lng', 'id_kesatuan', 'id_kesatuan_tujuan', 'keterangan'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }
   
    public function tujuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan_tujuan');
    }

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }

}
