<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    use HasFactory, UserTimezoneAware;
    protected $table = 'pers_pangkat';
    protected $fillable = ['pangkat', 'pangkat_lengkap'];
}
