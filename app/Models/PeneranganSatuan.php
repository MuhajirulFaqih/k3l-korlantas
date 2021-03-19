<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PeneranganSatuan extends Model
{
    protected $table = 'penerangan_satuan';

    protected $fillable = ['judul', 'keterangan'];

    public function files(){
        return $this->hasMany(PeneranganSatuanFile::class, 'id_penerangan_satuan');
    }

    public function scopeFiltered($query, $filter){
        if ($filter != null){
            $query->whereIn('id',
                    DB::table('penerangan_satuan')
                        ->select('id')
                        ->whereRaw("CONCAT(judul, '||', keterangan) LIKE ?", ['%'.$filter.'%'])
                );
        }

        return $query;
    }
}
