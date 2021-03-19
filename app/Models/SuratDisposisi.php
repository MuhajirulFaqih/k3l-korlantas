<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratDisposisi extends Model
{
    protected $table = 'surat_disposisi';

    protected $fillable = ['id_surat', 'w_diterima', 'w_disposisi', 'isi', 'file'];

    public function tujuan(){
        return $this->hasMany(SuratDisposisiTujuan::class, 'id_disposisi');
    }

    public function surat(){
        return $this->belongsTo(Surat::class, 'id_surat');
    }

    public function jabatan(){
        //return $this->hasManyThrough(Jabatan::class, SuratDisposisiTujuan::class, 'id_disposisi', 'id', 'id_jabatan');
        return $this->belongsToMany(Jabatan::class, 'surat_disposisi_tujuan', 'id_disposisi', 'id_jabatan');
    }
}
