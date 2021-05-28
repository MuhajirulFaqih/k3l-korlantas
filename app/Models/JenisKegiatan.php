<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class JenisKegiatan extends Model
{
    use HasFactory, NodeTrait, UserTimezoneAware;

    protected $table = 'jenis_kegiatan';

    protected $fillable = ['jenis', 'jenis_singkat', 'keterangan', 'id_parent'];

    public function kesatuan() {
        return $this->hasMany(JenisKegiatanKesatuan::class, 'id_jenis_kegiatan');
    }
}
