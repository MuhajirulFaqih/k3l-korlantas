<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Kejadian extends Model
{
    // use Searchable;
    protected $table = "kejadian";

    protected $fillable = [
        'id_user','kejadian','w_kejadian','lokasi','keterangan','lat','lng','id_darurat','verifikasi', 'selesai', 'follow_me'
    ];

    protected $casts = ['follow_me' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }

    public function komentar()
    {
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }

    public function tindak_lanjut(){
        return $this->hasMany(TindakLanjut::class, 'id_kejadian');
    }

    public function logmasyarakat(){
        return $this->morphMany(LogPosisiMasyarakat::class, 'posisi_masyarakat', 'jenis_induk', 'id_induk');
    }

    public function scopeBulan($query){
        $query->whereMonth('w_kejadian', Carbon::now()->month)
            ->whereYear('w_kejadian', Carbon::now()->year);
        return $query;
    }

    public function scopeFilteredUser($query, User $user){
        if ($user->jenis_pemilik == 'masyarakat')
            $query->where('id_user', $user->id);

        return $query;
    }

    public function nearby(){
        return $this->morphMany(PersonilTerdekat::class, 'personil_tedekat', 'jenis_induk', 'id_induk');
    }
    
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['waktu_kejadian'] = \Carbon\Carbon::parse($this->w_kejadian)->format('d F Y');
        return $array;
    }

    public function scopeFilterProsesPenanganan($query)
    {
        $query->whereRaw('id IN (select id_kejadian as id from tindak_lanjut
                            where tindak_lanjut.status = "proses_penanganan"
                            order by created_at desc)');
        $query->whereRaw('id NOT IN (select id_kejadian as id from tindak_lanjut
                            where tindak_lanjut.status = "selesai"
                            order by created_at desc)');
        
        return $query;
    }

    public function scopeFilterSelesai($query)
    {
        $query->whereRaw('id IN (select id_kejadian as id from tindak_lanjut
                            where tindak_lanjut.status = "selesai"
                            order by created_at desc)');
        
        return $query;
    }

    public function scopeFilterBelumDitangani($query)
    {
        $query->whereRaw('id NOT IN (select id_kejadian as id from tindak_lanjut
                            order by created_at desc)');
        
        return $query;
    }

    public function scopeFiltered($query, $filter, $status)
    {
        if($filter == null && ($status == null || $status == 'semua'))
            return $query;

        $idBulan = array ('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $enBulan = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $filter = str_ireplace($idBulan, $enBulan, $filter);

        return $query->whereIn('id', 
                DB::table('kejadian as k')
                    ->select('k.id')
                    ->where(function($sub) use ($status) {
                        $this->queryStatus($sub, $status);
                    })
                   ->whereRaw("CONCAT(
                        k.kejadian, '||', k.lokasi, '||', DATE_FORMAT(LEFT(k.w_kejadian, 10), '%d %M %Y'), '||', LEFT(k.w_kejadian, 10)
                    ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                    ->get()->pluck('id')->all()
                );
    }

    public function queryStatus($sub, $status)
    {
        switch ($status) {
            case 'selesai':
                $sub->whereRaw('k.id IN (select id_kejadian as id from tindak_lanjut where tindak_lanjut.status = "selesai" and tindak_lanjut.id_kejadian  = k.id)');
                break;
            
            case 'proses_penanganan':
                $sub->whereRaw('k.id IN (select id_kejadian as id from tindak_lanjut where tindak_lanjut.status = "proses_penanganan" and tindak_lanjut.id_kejadian  = k.id order by created_at desc)');
                $sub->whereRaw('k.id NOT IN (select id_kejadian as id from tindak_lanjut where tindak_lanjut.status = "selesai" and tindak_lanjut.id_kejadian  = k.id)');
                break;
            
            case 'laporan_baru':
                $sub->where('k.verifikasi', '1');
                $sub->whereRaw('k.id NOT IN (select id_kejadian as id from tindak_lanjut where tindak_lanjut.id_kejadian  = k.id )');
                break;

            case 'belum_diverifikasi':
                $sub->whereNull('k.verifikasi');
                $sub->whereRaw('k.id NOT IN (select id_kejadian as id from tindak_lanjut where tindak_lanjut.id_kejadian  = k.id)');
                break;
            
            default:
                $sub->whereNotNull('k.id');
                break;
        }
    }
}
