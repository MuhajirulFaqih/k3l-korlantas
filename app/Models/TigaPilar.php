<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class TigaPilar extends Model
{
    use Searchable;

    public $asYouType = false;

    protected $table = 'tiga_pilar';

    protected $fillable = ['id_deskel', 'nama', 'no_telp', 'jabatan', 'foto'];

    //protected $with = ['kelurahan'];

    public function kelurahan(){
        return $this->belongsTo(Kelurahan::class, 'id_deskel');
    }

    public function scopePerKelurahan($query, $filter, $id_kel){

        $query->where('id_deskel', '=', $id_kel);


        $queryFilter = DB::table('tiga_pilar')
            ->select('tiga_pilar.id')
            ->leftJoin('wil_kelurahan', 'tiga_pilar.id_deskel', 'wil_kelurahan.id_kel')
            ->leftJoin('wil_kecamatan', 'wil_kelurahan.id_kec', 'wil_kecamatan.id_kec');

        if($filter)
            $queryFilter->whereRaw("CONCAT(tiga_pilar.nama, '||', tiga_pilar.jabatan, '||', tiga_pilar.no_telp, '||', wil_kelurahan.nama, '||', wil_kecamatan.nama) LIKE ?", ['%'.addslashes($filter).'%']);

        if ($filter)
            $query->whereIn('id', $queryFilter);

        return $query;
    }

    public function scopeFiltered($query, $filter, $id_kec, $id_kel){
        $queryFilter = DB::table('tiga_pilar')
            ->select('tiga_pilar.id')
            ->leftJoin('wil_kelurahan', 'tiga_pilar.id_deskel', 'wil_kelurahan.id_kel')
            ->leftJoin('wil_kecamatan', 'wil_kelurahan.id_kec', 'wil_kecamatan.id_kec');

        if ($id_kec)
            $queryFilter->where("wil_kecamatan.id_kec", $id_kec);

        if ($id_kel)
            $queryFilter->where("wil_kelurahan.id_kel", $id_kel);

        if($filter)
            $queryFilter->whereRaw("CONCAT(tiga_pilar.nama, '||', tiga_pilar.jabatan, '||', tiga_pilar.no_telp, '||', wil_kelurahan.nama, '||', wil_kecamatan.nama) LIKE ?", ['%'.addslashes($filter).'%']);


        if ($filter || $id_kec || $id_kel)
            $query->whereIn('id', $queryFilter);

        return $query;
    }

    public function toSearchableArray(){
        $array = $this->toArray();
        return $array;
    }
}