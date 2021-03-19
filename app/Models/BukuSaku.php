<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuSaku extends Model
{
    protected $table = 'buku_saku';
    protected $fillable = ['judul', 'file'];
}
