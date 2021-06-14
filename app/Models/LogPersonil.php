<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogPersonil extends Model
{
    use HasFactory, UserTimezoneAware;
    protected $table = 'log_status_personil';
    protected $fillable = ['id_personil', 'status_dinas', 'waktu_mulai_dinas', 'waktu_selesai_dinas'];

    protected $casts = ['waktu_mulai_dinas' => 'datetime', 'waktu_selesai_dinas' => 'datetime'];

    public function personil()
    {
        return $this->belongsTo(Personil::class, 'id_personil');
    }

    public function status()
    {
        return $this->belongsTo(Dinas::class, 'status_dinas');
    }

    public function logpatroli(){
        return $this->hasMany(LogPatroli::class, 'id_status', 'id');
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('log_status_personil as l')
                ->select('l.id')
                ->join('personil as p', 'p.id', '=', 'l.id_personil')
                ->join('dinas as d', 'd.id', '=', 'l.status_dinas')
                ->join('pers_pangkat as pg', 'pg.id', '=', 'p.id_pangkat')
                ->join('pers_jabatan as j', 'j.id', '=', 'p.id_jabatan')
                ->join('pers_kesatuan as k', 'k.id', '=', 'p.id_kesatuan')
                ->whereRaw("CONCAT(
                    p.nrp, '||', p.nama, '||', pg.pangkat, '||', pg.pangkat_lengkap, '||', j.jabatan, '||', k.kesatuan, '||', k.induk, '||', d.kegiatan, '||', DATE_FORMAT(l.created_at, '%d %M %Y')
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
        );
    }

    public function scopeSearchStatus($query, $filter, $status)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('log_status_personil as l')
                ->select('l.id')
                ->join('personil as p', 'p.id', '=', 'l.id_personil')
                ->join('dinas as d', 'd.id', '=', 'l.status_dinas')
                ->join('pers_pangkat as pg', 'pg.id', '=', 'p.id_pangkat')
                ->join('pers_jabatan as j', 'j.id', '=', 'p.id_jabatan')
                ->join('pers_kesatuan as k', 'k.id', '=', 'p.id_kesatuan')
                ->where('l.status_dinas', $status)
                ->whereRaw("CONCAT(
                    p.nrp, '||', p.nama, '||', pg.pangkat, '||', pg.pangkat_lengkap, '||', j.jabatan, '||', k.kesatuan, '||', k.induk, '||', d.kegiatan, '||', DATE_FORMAT(l.created_at, '%d %M %Y')
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
        );
    }
}
