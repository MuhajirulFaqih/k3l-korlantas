<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Darurat extends Model
{
    use HasFactory;
    protected $table = 'darurat';
    protected $fillable = ['id_user', 'lat', 'lng', 'acc', 'selesai'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function scopeSemua($q){
        return $q;
    }

    public function nearby(){
        return $this->morphMany(PersonilTerdekat::class, 'personil_terdekat', 'jenis_induk', 'id_induk');
    }

    public function kejadian(){
        return $this->hasOne(Kejadian::class, 'id_darurat');
    }

    public function logmasyarakat(){
        return $this->morphMany(LogPosisiMasyarakat::class, 'posisi_masyarakat', 'jenis_induk', 'id_induk');
    }

    public function scopeFiltered($query, $filter, $status, $statusKejadian)
    {
        if($filter == null && $status == null)
            return $query;

        $idBulan = array ('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $enBulan = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $filter = str_ireplace($idBulan, $enBulan, $filter);

        return $query->whereIn('id',
            DB::table('darurat as d')
                ->select('d.id')
                ->join('user as u', 'u.id', '=', 'd.id_user')
                ->leftJoin('personil as p', function ($join){
                    $join->on('p.id', '=', 'u.id_pemilik')
                        ->on('u.jenis_pemilik', '=', DB::raw('\'personil\''));
                })
                ->leftJoin('masyarakat as m', function ($join){
                    $join->on('m.id', '=', 'u.id_pemilik')
                        ->on('u.jenis_pemilik', '=', DB::raw('\'masyarakat\''));
                })
                ->where(function($sub) use ($status, $statusKejadian) {
                    $this->queryStatus($sub, $status, $statusKejadian);
                })
                ->where(function($sub) use ($filter) {
                    $sub->where('p.nama', 'LIKE', '%'.$filter.'%')
                        ->orWhere('m.nama', 'LIKE', '%'.$filter.'%')
                        ->orWhere(DB::raw('DATE_FORMAT(d.created_at, "%d %M %Y")'), 'LIKE', '%'.$filter.'%');
                })
                ->get()->pluck('id')->all()
        );
    }

    public function queryStatus($sub, $status, $statusKejadian)
    {
        switch ($status) {
            case 'selesai':
                $sub->where('d.selesai', '1');
                break;
            case 'belum_selesai':
                $sub->where('d.selesai', '!=' ,'1');
                break;
            default:
                $sub->whereNotNull('d.id');
                break;
        }

        switch ($statusKejadian) {
            case 'belum_diubah':
                $sub->whereRaw('d.id NOT IN (select id_darurat as id from kejadian where kejadian.id_darurat  = d.id)');
                break;
            case 'ubah_kejadian':
                $sub->whereRaw('d.id IN (select id_darurat as id from kejadian where kejadian.id_darurat  = d.id)');
                break;
            default:
                $sub->whereNotNull('d.created_at');
                break;
        }
    }

}
