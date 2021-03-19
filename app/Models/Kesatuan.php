<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Kesatuan extends Model
{
    use SoftDeletes;
    
    protected $table = 'pers_kesatuan';

    protected $fillable = ['kesatuan', 'induk', 'email_polri', 'banner_grid'];

    public function email(){
        return $this->hasMany(EmailTrPenerima::class, 'id_kesatuan');
    }

    public function personil(){
        return $this->hasMany(Personil::class, 'id_kesatuan');
    }

    public function htchannel(){
        return $this->hasMany(KesatuanHtChannel::class, 'id_kesatuan');
    }

    public function ambilId(Kesatuan $kesatuan){
        $personil = $kesatuan->personil;

        $ids = [];

        foreach($personil as $row){
            if ($row->auth !== null) {
                if ($row->auth->fcm_id !== null && !empty($row->auth->fcm_id))
                    $ids[] = $row->auth->fcm_id;
            } else if ($row->bhabin !== null && $row->bhabin->auth !== null) {
                if ($row->bhabin->auth->fcm_id !== null && !empty($row->bhabin->auth->fcm_id))
                    $ids[] = $row->bhabin->auth->fcm_id;
            }
        }

        return collect($ids);
    }

    public function ambilToken(Kesatuan $kesatuan){
        $personil = $kesatuan->personil;

        $token = [];
        foreach($personil as $row){
            if ($row->auth !== null) {
                if ($row->auth->fcm_id !== null && !empty($row->auth->fcm_id))
                    $token[] = $row->auth->fcm_id;
            } else if ($row->bhabin !== null && $row->bhabin->auth !== null) {
                if ($row->bhabin->auth->fcm_id !== null && !empty($row->bhabin->auth->fcm_id))
                    $token[] = $row->bhabin->auth->fcm_id;
            }
        }

        return collect($token);
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('pers_kesatuan as k')
                ->select('k.id')
                ->whereNull('k.deleted_at')
                ->whereRaw("CONCAT(
                    k.kesatuan, '||', k.induk
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
            );
    }
}
