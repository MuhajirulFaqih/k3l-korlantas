<?php

namespace App\Transformers;

use App\Models\MasyarakatKegiatanBhabin;
use League\Fractal\TransformerAbstract;

class MasyarakatKegiatanBhabinTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(MasyarakatKegiatanBhabin $masyarakat)
    {
       return [
            'id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama' => $masyarakat->nama,
            'tempat_lahir' => $masyarakat->tempat_lahir,
            'tanggal_lahir' => $masyarakat->tanggal_lahir,
            'suku' => $masyarakat->suku,
            'agama' => $masyarakat->agama->agama,
            'alamat' => $masyarakat->alamat,
            'pekerjaan' => $masyarakat->pekerjaan,
            'no_hp' => $masyarakat->no_hp,
            'status_keluarga' => $masyarakat->status_keluarga,
        ];
    }
}
