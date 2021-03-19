<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informasi extends Model
{
    use SoftDeletes;
    protected $table = 'informasi';
    protected $fillable = ['informasi', 'aktif'];

    protected $dates = ['deleted_at'];
    protected $casts = ['aktif' => 'boolean'];

    public function scopeActive($query){
        $query->where('aktif', 1)
            ->orderBy('created_at', 'desc');
        return $query;
    }
}
