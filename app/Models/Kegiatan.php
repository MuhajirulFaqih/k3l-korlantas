<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Kegiatan extends Model
{
    use HasFactory, FilterJenisPemilik;

    protected $table = 'kegiatan';
    protected $guarded = [];

    protected $casts = ['waktu_kegiatan' => 'datetime'];

    public function jenis(){
        return $this->hasMany(KegiatanJenisKegiatan::class, 'id_kegiatan');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function komentar(){
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }


    public function scopeLaporan($query, $filter, $rentang, $id_jenis){
        if ($filter){
            $query->whereIn('id',
                DB::table('kegiatan')
                    ->selectRaw('kegiatan.id as id')
                    ->leftJoin('kegiatan_jenis_kegiatan', 'kegiatan_jenis_kegiatan.id_kegiatan', '=', 'kegiatan.id')
                    ->leftJoin('jenis_kegiatan', 'jenis_kegiatan.id', '=', 'kegiatan_jenis_kegiatan.id_jenis_kegiatan')
                    ->join('user', 'user.id', '=', 'kegiatan.id_user')
                    ->join('personil', 'personil.id', '=', 'user.id_pemilik', 'left')
                    ->whereRaw('CONCAT (kegiatan.detail,\'||\', jenis_kegiatan.jenis, \'||\', personil.nama, \'||\', personil.nrp)')
                    ->get()
                    ->groupBy('kegiatan.id')
                    ->pluck('id')
                    ->all()
            );
        }

        if ($rentang){
            list($mulai, $selesai) = $rentang;
            $query->whereBetween('waktu_kegiatan', [substr($mulai, 0, 10), substr($selesai, 0, 10)]);
        }

        // if ($id_jenis){
        //     $query->where('id_jenis', $id_jenis);
        // }

        return $query;
    }
    
    public function scopeFilter($query, $filter){
        if ($filter){
            $query->whereIn('id',
                DB::table('kegiatan')
                    ->select('kegiatan.id')
                    ->leftJoin('kegiatan_jenis_kegiatan', 'kegiatan_jenis_kegiatan.id_kegiatan', '=', 'kegiatan.id')
                    ->leftJoin('jenis_kegiatan', 'jenis_kegiatan.id', '=', 'kegiatan_jenis_kegiatan.id_jenis_kegiatan')
                    ->leftJoin('user', 'user.id', '=', 'kegiatan.id_user')
                    ->leftJoin('personil', 'personil.id', '=', 'user.id_pemilik')
                    ->where(function($sub) use ($filter) {
                        $sub->whereRaw("CONCAT_WS(
                                kegiatan.detail,'||', jenis_kegiatan.jenis, '||', personil.nama, '||', personil.nrp
                            ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%']);
                    })
                    ->get()
                    ->pluck('id')
                    ->all()
            );
        }

        return $query;
    }

    public function scopeFilterQuickResponse($query, $is_quick_response)
    {
        if($is_quick_response) {
            return $query->where('is_quick_response', 1);
        }
        return $query->where('is_quick_response', 0);
    }
    
    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }

    public function kelurahan(){
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan_binmas');
    }
}
