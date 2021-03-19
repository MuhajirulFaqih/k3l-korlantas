<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'komentar';

    protected $fillable = [
        'komentar','id_user', 'jenis_induk', 'id_induk'
    ];

    public function induk()
    {
        return $this->morphTo(null, 'jenis_induk', 'id_induk');
    }

     public function user()
     {
         return $this->belongsTo(User::class,'id_user')->withTrashed();
     }

     public function ambilIdOneSignal(Komentar $komentar){
        $induk = $komentar->induk;
        $idPemilikKiriman = $induk->id_user;
        $idPengirimKomentar = $komentar->id_user;

        $ids = [];
        foreach ($induk->komentar as $row){
            $kondisi = $row->id_user !== $idPemilikKiriman &&
                $row->id_user !== $idPengirimKomentar;

            if ($kondisi) {
                $ids[] = $row->user->id;
            }
        }

        $collection = collect($ids);

        return $collection->unique();
     }

     public function ambilIdPemilikOneSignal(Komentar $komentar){
        $induk = $komentar->induk;
        if ($komentar->id_user != $induk->user->id)
            return collect($induk->user->id);
        else return collect();
     }

     public function ambilToken(Komentar $komentar){
         $induk = $komentar->induk;
         $idPemilikKiriman = $induk->id_user;
         $idPengirimKomentar = $komentar->id_user;

         $tokens = [];
         foreach($induk->komentar as $row){
             $kondisi = $row->id_user !== $idPemilikKiriman && 
                    $row->id_user !== $idPengirimKomentar &&
                    !empty($row->user->fcm_id);
             
            if ($kondisi) {
                $tokens[] = $row->user->fcm_id;
            }
         }

         $collection = collect($tokens);
         return $collection->unique();
     }

     public function ambilTokenPemilik(Komentar $komentar){
         $induk = $komentar->induk;
         return collect($induk->user->fcm_id);
     }
}