<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KesatuanJenis extends Model
{
    use HasFactory, UserTimezoneAware;

    protected $table = 'pers_kesatuan_jenis';

    protected $guarded = [];
}
