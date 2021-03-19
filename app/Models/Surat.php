<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = ['tanggal', 'waktu_diterima', 'id_asal', 'no_agenda', 'nomor', 'pengirim', 'derajat', 'klasifikasi', 'perihal', 'file'];

    public function disposisi(){
        return $this->hasMany(SuratDisposisi::class, 'id_surat');
    }

    public function jenis(){
        return $this->belongsTo(SuratJenisAsal::class, 'id_asal');
    }

    public function tujuan(){
        return $this->hasMany(SuratDisposisiTujuan::class, 'id_surat');
    }

    public function jabatan(){
        return $this->belongsToMany(Jabatan::class, 'surat_disposisi_tujuan', 'id_surat', 'id_jabatan');
    }

    public function scopeFilter($query, $filter = null){
        if ($filter){
            $query->whereIn(
                'id',
                DB::table('surat')
                    ->select('id')
                    ->join('surat_disposisi', 'surat.id', 'surat_disposisi.id_surat')
                    ->join('surat_disposisi_tujuan', 'surat.id', 'surat_disposisi_tujuan.id_surat')
                    ->join('surat_jenis', 'surat.id_jenis', 'surat_jenis.id')
                    ->join('jabatan', 'jabatan.id', 'surat_disposisi_tujuan.id_jabatan')
                    ->whereRaw('CONCAT(surat.no_agenda, \'||\', surat.tanggal, \'||\', surat.pengirim, \'||\', surat.perihal, \'||\', surat.nomor, \'||\', surat_jenis.jenis, \'||\', jabatan.jabatan)')
                    ->get()->pluck('id')->unique()->all()
            );
        }

        return $query;
    }
}
