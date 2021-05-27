<?php
namespace App\Traits;

use App\Models\Kesatuan;

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
        return $query->whereIn('id_kesatuan', $id_kesatuan);
    }
}