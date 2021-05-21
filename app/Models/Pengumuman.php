<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumuman';
    protected $fillable = ['id_user', 'id_kesatuan', 'judul', 'file'];

    public function kesatuan(){
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }


    public function ambilIdOneSignal(Pengumuman $pengumuman){
        $kesatuan = $pengumuman->kesatuan;
        $id_kesatuan = Kesatuan::descendantsAndSelf($kesatuan->id)->pluck('id')->all();

        $personil = Personil::whereIn('id_kesatuan', $id_kesatuan)->get();

        $ids = [];

        foreach ($personil as $row){
            if ($row->auth)
                $ids[] = optional($row->auth)->id;
        }
        return collect($ids)->unique();
    }
}
