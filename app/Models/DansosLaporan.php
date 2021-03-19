<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DansosLaporan extends Model
{
    protected $table = 'dansos_laporan';

    protected $fillable = ['id_kel', 'kegunaan', 'kepada', 'jumlah', 'foto', 'tahun_anggaran'];

    public function kelurahan(){
        return $this->belongsTo(Kelurahan::class, 'id_kel');
    }

    public function scopeReport($query, $filter)
    {
    	if($filter->desa == '')
    		return $query;

    	return $query->whereIn(
            'id',
            DB::table('dansos_laporan')
                ->select('id')
                ->where(function($sub) use ($filter) {
                	if($filter->tipe != '1') {
                		$sub->whereYear('tahun_anggaran', $filter->tahun);
                	} else {
                		$sub->whereNotNull('id');
                	}
                })
                ->where('id_kel', $filter->desa)
                ->get()->pluck('id')->all()
            );
    }
}
