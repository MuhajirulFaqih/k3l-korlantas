<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KegiatanBhabin extends Model
{
    protected $table = 'kegiatan_bhabhin';
    protected $guarded = [];
    protected $casts = ['waktu_kegiatan' => 'datetime'];

    public function tipe()
    {
        return $this->belongsTo(IndikatorKegiatanBhabin::class, 'id_indikator');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisKegiatanBhabin::class, 'id_jenis');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKegiatanBhabin::class, 'id_kategori');
    }

    public function bidang()
    {
        return $this->belongsTo(BidangKegiatanBhabin::class, 'id_bidang');
    }

    public function masyarakat()
    {
        return $this->belongsToMany(MasyarakatKegiatanBhabin::class, 'kegiatan_masyarakat', 'id_kegiatan_bhabin', 'id_masyarakat');
    }
	
	public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kec');
    }

    public function scopeSorting($query, $request)
    {
        if($request->type == 1):
            $q = $query;
        else:
            list($mulai, $selesai) = $request->rentang;
            $mulai = date('Y-m-d', strtotime($mulai));
            $selesai = date('Y-m-d', strtotime($selesai));
            $q = $query->whereDate('waktu_kegiatan', '>=', $mulai)
                        ->whereDate('waktu_kegiatan', '<=', $selesai);
        endif;

        return $query;
    }
}
