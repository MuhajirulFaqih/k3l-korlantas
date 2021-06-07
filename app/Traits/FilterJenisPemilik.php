<?php
namespace App\Traits;

use App\Models\Kesatuan;
use Illuminate\Support\Facades\Log;

trait FilterJenisPemilik
{
	protected function jenisPemilik($query, $user)
    {
        if($user->jenis_pemilik == 'masyarakat') {
            return $query->where('id_user', $user->id);
        } elseif($user->jenis_pemilik == 'personil') {
            $kesatuan = $user->pemilik->kesatuan;
            $id_kesatuan = $kesatuan->level == 5 ? [$kesatuan->id, $kesatuan->parent->id] : [$kesatuan->id];
        } elseif($user->jenis_pemilik == 'kesatuan') {
            $kesatuan = $user->pemilik;
            $id_kesatuan = Kesatuan::descendantsAndSelf($kesatuan->id)->pluck('id')->all();
        } else {
            return $query;
        }
        Log::info("id_kesatuan", $id_kesatuan);
        return $query->whereIn('id_kesatuan', $id_kesatuan)->orWhereNull('id_kesatuan');
    }
    
    protected function pemilikKesatuan($query, $user)
    {
        if($user->jenis_pemilik == 'personil') {
            $kesatuan = $user->pemilik->kesatuan;
            $id_kesatuan = $kesatuan->level == 5 ? [$kesatuan->id, $kesatuan->parent->id] : [$kesatuan->id];
        } elseif($user->jenis_pemilik == 'kesatuan') {
            $kesatuan = $user->pemilik;
            $id_kesatuan = Kesatuan::descendantsAndSelf($kesatuan->id)->pluck('id')->all();
        } else {
            return $query;
        }
        Log::info("id", $id_kesatuan);
        return $query->whereIn('id', $id_kesatuan);
    }
}
