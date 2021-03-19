<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Masyarakat extends Model
{
    use SoftDeletes;
    
    protected $table = "masyarakat";

    protected $fillable = ['id_kel', 'nik', 'nama', 'foto', 'alamat', 'no_telp', 'provider', 'provider_id'];

    public function auth(){
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function kelurahan(){
        return $this->belongsTo(Kelurahan::class, 'id_kel', 'id_kel');
    }

    public function ambilId(){
        $masyarakat = $this->get();

        $ids = [];

        foreach ($masyarakat as $row){
            if ($row->auth !== null)
                $ids = $row->auth->id;
        }

        return collect($ids);
    }

    public function ambilToken(){
        $masyarakat = $this->get();
        $tokens = [];

        foreach ($masyarakat as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }

        return collect($tokens);
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('masyarakat as m')
                ->select('m.id')
                ->whereNull('m.deleted_at')
                ->whereRaw("LOWER(CONCAT_WS(
                    IFNULL(m.nik, ''), '||', m.nama, '||', m.alamat, '||', m.no_telp
                )) LIKE ?", ['%' . addcslashes(strtolower($filter), '%_') . '%'])
                ->get()->pluck('id')->all()
            );
    }
}
