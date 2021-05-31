<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Absensi extends Model
{
    use HasFactory, FilterJenisPemilik, UserTimezoneAware;

    protected $table = 'absensi_personil';
    protected $guarded = [];
    protected $date = ['waktu_mulai', 'waktu_selesai'];

    public function personil(){
        return $this->belongsTo(Personil::class, 'id_personil');
    }

    public function scopeFilterLaporan($query, $rentang, $kesatuan, $nrp) {
        if ($rentang != '') {
            list($mulai, $selesai) = $rentang;
            $query->whereDate('created_at', '>=', $mulai)
                    ->whereDate('created_at', '<=', $selesai);
        }

        if (count($kesatuan) != 0 || $nrp != '') {
            $query->whereIn('id',
                    DB::table('absensi_personil as a')
                    ->select('a.id')
                    ->join('personil as p', 'p.id', '=', 'a.id_personil')
                    ->join('pers_pangkat as pg', 'pg.id', '=', 'p.id_pangkat')
                    ->join('pers_jabatan as j', 'j.id', '=', 'p.id_jabatan')
                    ->join('pers_kesatuan as k', 'k.id', '=', 'p.id_kesatuan')
                    ->where(function($query) use ($kesatuan, $nrp) {
                        if(count($kesatuan) != 0) { $query->whereIn('p.id_kesatuan', $kesatuan); }
                        if($nrp != '') { $query->where('p.nrp', 'LIKE', '%'.$nrp.'%'); }
                    })
                    ->groupBy('id')
                    ->get()
                    ->pluck('id')
                    ->all()
            );
        }

        return $query;
    }

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }
}
