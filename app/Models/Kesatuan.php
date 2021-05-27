<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;

class Kesatuan extends Model
{
    use HasFactory, NodeTrait;

    protected $table = 'pers_kesatuan';

    protected $fillable = ['kesatuan', 'level', 'kode_satuan', 'id_jenis_kesatuan'];

    public function auth(){
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function personil(){
        return $this->hasMany(Personil::class, 'id_kesatuan');
    }

    public function htchannel(){
        return $this->hasMany(KesatuanHTChannel::class, 'id_kesatuan');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisKesatuan::class, 'id_jenis_kesatuan');
    }
    
    public function kegiatan()
    {
        return $this->hasMany(JenisKegiatanKesatuan::class, 'id_kesatuan');
    }

    public function scopeFiltered($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('pers_kesatuan as k')
                ->select('k.id')
                ->whereNull('k.deleted_at')
                ->whereRaw("CONCAT(
                    k.kesatuan
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
            );
    }
}
