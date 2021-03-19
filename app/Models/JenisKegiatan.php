<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    protected $table = 'kegiatan_jenis';

    const CREATED_AT = 'w_tambah';

    const UPDATED_AT = 'w_ubah';

    protected $fillable = ['jenis', 'jenis_singkat'];

}
