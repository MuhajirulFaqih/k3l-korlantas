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
    
    public function scopeFilterQuickResponse($query)
    {
        $id_jenis_quick_response = 
        DB::table('jenis_kegiatan_kesatuan as jk')
            ->select('jk.id_jenis_kegiatan')
            ->join('pers_kesatuan as k', 'jk.id_kesatuan', '=', 'k.id')
            ->where('k.id_jenis_kesatuan', 8)
            ->groupBy('jk.id_jenis_kegiatan')
            ->get()->pluck('id_jenis_kegiatan')->all();

        $id_patroli_beat = 
        DB::table('jenis_kegiatan as j')
            ->select('j.id')
            ->where('j.jenis', 'PATROLI BEAT')
            ->get()->pluck('id')->all();
    
        return $query->whereIn('id', array_merge($id_jenis_quick_response, $id_patroli_beat));
    }
}
