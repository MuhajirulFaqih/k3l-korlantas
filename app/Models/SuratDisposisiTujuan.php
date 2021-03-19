<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratDisposisiTujuan extends Model
{
    protected $table = 'surat_disposisi_tujuan';

    protected $fillable = ['id_surat', 'id_disposisi', 'id_jabatan'];

    public function surat(){
        return $this->belongsTo(Surat::class, 'id_surat');
    }

    public function disposisi(){
        return $this->belongsTo(SuratDisposisi::class, 'id_disposisi');
    }

    public function jabatan(){
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }
}
