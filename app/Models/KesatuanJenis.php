<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KesatuanJenis extends Model
{
    use HasFactory;

    protected $table = 'pers_kesatuan_jenis';

    protected $guarded = [];
}
