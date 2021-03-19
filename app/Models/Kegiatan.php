<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Kegiatan extends Model
{
    // use Searchable;

    protected $table = 'kegiatan';

    protected $fillable = ['id_user', 'tipe_laporan', 'waktu_kegiatan', 'lat', 'lng', 'jenis_kegiatan', 'judul', 'sasaran', 'lokasi', 'kuat_pers', 'hasil', 'jml_giat', 'jml_tsk', 'bb', 'perkembangan', 'dasar', 'tsk_bb', 'modus', 'keterangan', 'dokumentasi'];

    protected $casts = ['waktu_kegiatan' => 'datetime'];

    public function tipe()
    {
        return $this->belongsTo(TipeLaporan::class, 'tipe_laporan');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisKegiatan::class, 'jenis_kegiatan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }

    public function komen()
    {
        return $this->belongsTo(Komentar::class, 'id');
    }

    public function komentar()
    {
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['pangkat'] = $this->user->pemilik->pangkat->pangkat['pangkat'] ?? null;
        $array['tipe'] = $this->tipe['tipe'];
        $array['waktu_dibuat'] = \Carbon\Carbon::parse($this->waktu_kegiatan)->format('d F Y');
        return $array;
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }
        $idBulan = array ('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $enBulan = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $filter = str_ireplace($idBulan, $enBulan, $filter);

        return $query->whereIn(
            'id',
            DB::table('kegiatan as k')
                ->select('k.id')
                ->leftJoin('tipe_laporan as t', 'k.tipe_laporan', '=', 't.id')
                ->leftJoin('kegiatan_jenis as j', 'k.jenis_kegiatan', '=', 'j.id')
                ->where(function($sub) use ($filter) {
                    $sub->whereRaw("CONCAT_WS(
                            k.judul, '||', k.lokasi, '||', CONCAT(k.kuat_pers, ' Personil'), '||', 
                            k.keterangan, '||', t.tipe, '||', DATE_FORMAT(LEFT(k.waktu_kegiatan, 10), '%d %M %Y'), '||', LEFT(k.waktu_kegiatan, 10)
                        ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%']);
                })
                ->get()->pluck('id')->all()
            );
    }
}