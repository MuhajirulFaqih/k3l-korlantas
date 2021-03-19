<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Pendapatan extends Model
{
    use SoftDeletes;

    protected $table = 'pendapatan';
    protected $fillable = ['id_kel', 'bagihasilpajakdaerah', 'pendapatanaslidaerah', 'alokasidanadesa', 'tahun_anggaran', 'silpa'];

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel');
    }

    public function scopeReport($query, $filter)
    {
    	if($filter->desa == '')
    		return $query;

    	return $query->whereIn(
            'id',
            DB::table('pendapatan')
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
