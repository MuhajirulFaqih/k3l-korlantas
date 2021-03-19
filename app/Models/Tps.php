<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tps extends Model
{
    protected $table = 'tps';

    protected $fillable = ['id_deskel', 'id_kesatuan', 'nama', 'ket', 'tidak_sah', 'bap', 'lat', 'lng'];


    public function personil(){
        return $this->belongsToMany(Personil::class, 'tps_personil', 'id_tps', 'id_personil');
    }

    public function paslon(){
        return $this->hasMany(Paslon::class, 'id_deskel', 'id_deskel');
    }

    public function perolehan_suara(){
        return $this->hasMany(PerolehanSuara::class, 'id_tps');
    }

    public function deskel(){
        return $this->belongsTo(Kelurahan::class, 'id_deskel', 'id_kel');
    }

    public function kesatuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }

    public function scopeFiltered($query, $filter, $id_kec, $id_kel)
    {
        $tableQuery = DB::table('tps')
            ->select('tps.id')
            ->join('wil_kelurahan', 'tps.id_deskel', '=', 'wil_kelurahan.id_kel')
            ->join('wil_kecamatan', 'wil_kelurahan.id_kec', '=', 'wil_kecamatan.id_kec');

        if ($id_kec)
            $tableQuery->where('wil_kecamatan.id_kec', $id_kec);

        if ($id_kel)
            $tableQuery->where('wil_kelurahan.id_kel', $id_kel);

        if ($filter)
            $tableQuery->whereRaw("CONCAT(tps.nama, '||', wil_kelurahan.nama, '||', wil_kecamatan.nama) like ?", ['%' . $filter . '%']);

        if ($filter || $id_kec || $id_kel)
            $query->whereIn('id', $tableQuery);

        return $query;
    }

    public function getPam()
    {
        $db = DB::table('tps')
                    ->select('tps_personil.id')
                    ->join('tps_personil', 'tps.id', '=', 'tps_personil.id_tps')
                    ->whereNotNull('tps.lat')
                    ->whereNotNull('tps.lng')
                    ->groupBy('tps_personil.id_personil')
                    ->get()
                    ->count();
        return $db;
    }

}
