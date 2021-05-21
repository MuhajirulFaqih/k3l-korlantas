<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    use HasFactory;

    protected $table = "tindak_lanjut";

    protected $fillable = ['id_user', 'id_kejadian', 'status', 'keterangan', 'foto'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kejadian(){
        return $this->belongsTo(Kejadian::class, 'id_kejadian');
    }

    public function ambilIdPemilik(TindakLanjut $tindakLanjut){
        $induk = $tindakLanjut->kejadian;
        return collect($induk->user->id);
    }

    public function ambilId(TindakLanjut $tindaklanjut) {
        $induk = $tindaklanjut->kejadian;
        $idPemilikKiriman = $induk->id_user;
        $idPengirim = $tindaklanjut->id_user;

        $token = [];
        foreach ($induk->komentar as $row) {
            $kondisi = $row->id_user !== $idPemilikKiriman &&
                $row->id_user !== $idPengirim;
            if ($kondisi) {
                $tokens[] = $row->user->id;
            }
        }

        $collection = collect($token);
        return $collection->unique();
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
