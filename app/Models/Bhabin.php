<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bhabin extends Model
{
    use SoftDeletes;
    protected $table = 'bhabin';

    protected $with = ['personil', 'kelurahan'];

    protected $fillable = [
        'id_personil'
    ];

    public function auth()
    {
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function personil()
    {
        return $this->hasOne(Personil::class, 'id', 'id_personil')->withTrashed();
    }

    public function kelurahan()
    {
        return $this->belongsToMany(Kelurahan::class, 'bhabin_kelurahan', 'id_bhabin', 'id_kel');
    }

    public function getBhabinKelAttribute()
    {
        return $this->kelurahan->implode(function ($o) {
            return $o->nama . '-' . $o->kecamatan->nama;
        }, ', ');
    }

    public function ambilId(){
        $ids = User::where('jenis_pemilik', 'bhabin')->get()->pluck('id')->all();

        return collect($ids);
    }

    public function ambilIdLain($id){
        $ids = User::where('jenis_pemilik', 'bhabin')->where('id_pemilik', '<>', $id)->get()->pluck('id')->all();

        return collect($ids);
    }

    public function ambilToken()
    {
        /*$bhabin = $this->all();

        $tokens = [];
        foreach ($bhabin as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }*/

        $tokens = User::where('jenis_pemilik', 'bhabin')->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();

        return collect($tokens);
    }


    public function ambilTokenLain($id)
    {
        /*$bhabin = $this->where('id', '<>', $id)->get();

        $tokens = [];
        foreach ($bhabin as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }*/

        $tokens = User::where('jenis_pemilik', 'bhabin')->where('id_pemilik', '<>', $id)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();

        return collect($tokens);
    }

    public function ambilTokenByKesatuan($kesatuan)
    {
        $bhabin = $this->personil()->whereIn('id_kesatuan', $kesatuan);

        $tokens = [];
        foreach ($bhabin as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }

        return collect($tokens);
    }

    public function ambilTokenById($id_personil)
    {
        $bhabin = $this->whereIn('id_personil', $id_personil);
        $tokens = [];
        foreach ($bhabin as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }
        return collect($tokens);
    }
}
