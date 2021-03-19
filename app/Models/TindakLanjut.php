<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    protected $table = "tindak_lanjut";

    protected $fillable = ['id_user', 'id_kejadian', 'status', 'keterangan', 'foto'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }

    public function kejadian(){
        return $this->belongsTo(Kejadian::class, 'id_kejadian');
    }

    public function ambilIdPemilik(TindakLanjut $tindakLanjut){
        $induk = $tindakLanjut->kejadian;
        return collect($induk->user->id);
    }

    public function ambilId(TindakLanjut $tindakLanjut){
        $induk = $tindakLanjut->kejadian;
        $idPemilikKiriman = $induk->id_user;
        $idPengirim = $tindakLanjut->id_user;

        $ids = [];
        foreach ($induk->tindak_lanjut as $row){
            $kondisi = $row->id_user !== $idPemilikKiriman && $row->id_user !== $idPengirim;

            if ($kondisi){
                $ids = $row->user->id;
            }
        }

        return collect($ids)->unique();
    }

    public function ambilTokenPemilik(TindakLanjut $tindaklanjut){
        $induk = $tindaklanjut->kejadian;
        return collect($induk->user->fcm_id);
    }

    public function ambilToken(TindakLanjut $tindaklanjut) {
        $induk = $tindaklanjut->kejadian;
        $idPemilikKiriman = $induk->id_user;
        $idPengirim = $tindaklanjut->id_user;

        $token = [];
        foreach ($induk->komentar as $row) {
            $kondisi = $row->id_user !== $idPemilikKiriman &&
                $row->id_user !== $idPengirim &&
                !empty($row->user->fcm_id);
            if ($kondisi) {
                $tokens[] = $row->user->fcm_id;
            }
        }

        $collection = collect($token);
        return $collection->unique();
    }
}
