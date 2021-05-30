<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Kejadian extends Model
{
    use HasFactory, FilterJenisPemilik, UserTimezoneAware;

    protected $table = 'kejadian';
    protected $fillable = [
        'id_user', 'kejadian', 'w_kejadian', 'lokasi', 'keterangan', 'lat', 'lng', 'id_darurat', 'id_kesatuan', 'verifikasi', 'follow_me', 'selesai'
    ];

    protected $casts = ['w_kejadian' => 'datetime'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function komentar(){
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }

    public function tindak_lanjut(){
        return $this->hasMany(TindakLanjut::class, 'id_kejadian');
    }

    public function nearby(){
        return $this->morphMany(PersonilTerdekat::class, 'personil_terdekat', 'jenis_induk', 'id_induk');
    }

    public function logmasyarakat(){
        return $this->morphMany(LogPosisiMasyarakat::class, 'posisi_masyarakat', 'jenis_induk', 'id_induk');
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

    public function scopeFilteredUser($query, User $user){
        if ($user->jenis_pemilik == 'masyarakat')
            $query->where('id_user', $user->id);

        return $query;
    }


    public function scopeFilteredCetak($query, $type, $range) {
        if($type == '' || ($type == '2' && $range == '')):
            return $query->whereNull('id');
        elseif($type == 1):
            return $query;
        elseif($type == 2):
            list($mulai, $selesai) = $range;
            $mulai = date('Y-m-d', strtotime($mulai));
            $selesai = date('Y-m-d', strtotime($selesai));
            if(substr($mulai, 0, 10) == substr($selesai, 0, 10)):
                return $query->whereDate('created_at', substr($selesai, 0, 10));
            else:
                return $query->whereBetween('created_at', [substr($mulai, 0, 10), substr($selesai, 0, 10)]);
            endif;
        endif;
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

    public function scopeFilterLaporan($query, $rentang) {
        if ($rentang != '') {
            list($mulai, $selesai) = $rentang;
            $query->whereDate('w_kejadian', '>=', $mulai)
                    ->whereDate('w_kejadian', '<=', $selesai);
        }

        return $query;
    }

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }
}
