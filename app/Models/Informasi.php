<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informasi extends Model
{
    use HasFactory, UserTimezoneAware;
    protected $table = 'informasi';
    protected $fillable = ['informasi', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeActive($query){
        $query->where('aktif', 1)
            ->orderBy('created_at', 'desc');
        return $query;
    }
}
