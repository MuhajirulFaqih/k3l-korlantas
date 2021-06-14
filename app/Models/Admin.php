<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use HasFactory, SoftDeletes, UserTimezoneAware;
    protected $table = 'admin';

    protected $fillable = ['nama', 'status', 'in_call', 'visibility'];

    public function auth(){
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function scopeVisible($query)
    {
        return $query->where("visiblility", 1);
    }
}
