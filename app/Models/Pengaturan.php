<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['nama', 'nilai'];

    protected $casts = ['autoload' => 'boolean'];
    
    public function scopeGetByKey($query, $nama){
        return $query->where('nama', $nama);
    }
}
