<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\FilterJenisPemilik;

class Pengaduan extends Model
{
    use HasFactory, FilterJenisPemilik;
    protected $table = 'pengaduan';

    protected $fillable = ['id_user', 'lat', 'lng', 'lokasi', 'id_kesatuan', 'keterangan', 'foto'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function komentar()
    {
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }

    public function scopeFilter($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }
        $idBulan = array ('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $enBulan = array ('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $filter = str_ireplace($idBulan, $enBulan, $filter);

        return $query->whereIn(
            'id',
            DB::table('pengaduan as p')
                ->select('p.id')
                ->leftJoin('user as u', function($join) {
                    $join->on('u.id', '=', 'p.id_user')
                        ->on('u.jenis_pemilik', '=', DB::raw('\'masyarakat\''));
                })
                ->leftJoin('masyarakat as m', 'm.id', '=', 'u.id_pemilik')
                ->whereRaw("CONCAT(
                    p.lokasi, '||', p.keterangan, '||', m.nama, '||', DATE_FORMAT(LEFT(p.created_at, 10), '%d %M %Y'), '||', LEFT(p.created_at, 10)
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
        );
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

    public function scopeFilterJenisPemilik($query, $user)
    {
        return $this->jenisPemilik($query, $user);
    }
}
