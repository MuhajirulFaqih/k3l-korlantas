<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeneranganSatuanFile extends Model
{
    protected $table = 'penerangan_satuan_file';

    protected $fillable = ['file', 'type', 'thumbnails', 'file_name'];
}
