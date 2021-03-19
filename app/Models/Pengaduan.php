<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Pengaduan extends Model
{
	// use Searchable;

    protected $table = 'pengaduan';

    protected $fillable = ['id_user', 'lat', 'lng', 'lokasi', 'keterangan', 'foto'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }

    public function komentar()
    {
        return $this->morphMany(Komentar::class, 'komentar', 'jenis_induk', 'id_induk');
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['pangkat'] = $this->user->pemilik->pangkat->pangkat['pangkat'] ?? null;
        $array['waktu_dibuat'] = \Carbon\Carbon::parse($this->created_at)->format('d F Y');
        return $array;
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
}
