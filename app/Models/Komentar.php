<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    use HasFactory, UserTimezoneAware;
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
        return $this->belongsTo(User::class,'id_user');
    }

    public function ambilIdPemilikOneSignal(Komentar $komentar){
        $induk = $komentar->induk;
        $idPemilikKiriman = $induk->id_user;
        $idPengirimKomentar = $komentar->id_user;

        if ($idPemilikKiriman != $idPengirimKomentar)
            return collect($idPemilikKiriman);

        return collect();
    }

    public function ambilIdOneSignal(Komentar $komentar){
        $induk = $komentar->induk;
        $idPemilikKiriman = $induk->id_user;
        $idPengirimKomentar = $komentar->id_user;

        $tokens = [];
        foreach($induk->komentar as $row){
            $kondisi = $row->id_user !== $idPemilikKiriman &&
                $row->id_user !== $idPengirimKomentar;

            if ($kondisi) {
                $tokens[] = $row->user->id;
            }
        }

        return collect(array_values(array_unique($tokens)));
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
