<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Facades\DB;

class JenisKegiatan extends Model
{
    use HasFactory, NodeTrait, UserTimezoneAware;

    protected $table = 'jenis_kegiatan';

    protected $fillable = ['jenis', 'jenis_singkat', 'keterangan', 'id_parent'];

    public function kesatuan() {
        return $this->hasMany(JenisKegiatanKesatuan::class, 'id_jenis_kegiatan');
    }

    public function scopeFilterKegiatan($query)
    {
        return $query->whereIn(
            'id',
            DB::table('jenis_kegiatan_kesatuan as j')
                ->select('j.id_jenis_kegiatan')
                ->join('pers_kesatuan as k', 'j.id_kesatuan', '=', 'k.id')
                ->where('k.id_jenis_kesatuan', 5)
                ->groupBy('j.id_jenis_kegiatan')
                ->get()->pluck('id_jenis_kegiatan')->all()
            );
    }
}
