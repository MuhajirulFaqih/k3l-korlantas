<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Absensi extends Model
{
    use HasFactory, UserTimezoneAware;

    protected $table = 'absensi_personil';
    protected $guarded = [];
    protected $date = ['waktu_mulai', 'waktu_selesai'];

    public function personil(){
        return $this->belongsTo(Personil::class, 'id_personil');
    }

    public function scopeFiltered($query, $kesatuan, $tanggal, $nrp){
        if ($kesatuan == null && $tanggal[0] == null && $nrp == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('absensi_personil as a')
                ->select('a.id')
                ->join('personil as p', 'p.id', '=', 'a.id_personil')
                ->join('pers_pangkat as pg', 'pg.id', '=', 'p.id_pangkat')
                ->join('pers_jabatan as j', 'j.id', '=', 'p.id_jabatan')
                ->join('pers_kesatuan as k', 'k.id', '=', 'p.id_kesatuan')
                ->where(function($query) use ($kesatuan, $tanggal, $nrp) {
                    if($kesatuan != '') {
                        $query->where('p.id_kesatuan', '=', $kesatuan);
                    }
                    if($tanggal[0] != null) {
                        list($mulai, $selesai) = $tanggal;
                        $mulai = date('Y-m-d', strtotime($mulai));
                        $selesai = date('Y-m-d', strtotime($selesai));

                        $query->whereBetween(DB::raw('DATE(a.created_at)'), [ $mulai, $selesai ]);
                    }
                    if($nrp != '') {
                        $query->where('p.nrp', 'LIKE', '%'.$nrp.'%');
                    }
                })
                ->get()->pluck('id')->all()
        );
    }
}
