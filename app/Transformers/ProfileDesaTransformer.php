<?php

namespace App\Transformers;

use App\Models\ProfilKelurahan;
use League\Fractal\TransformerAbstract;

class ProfileDesaTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param ProfilKelurahan|null $kelurahan
     * @return array
     */
    public function transform(ProfilKelurahan $kelurahan)
    {
        return [
            'id' => optional($kelurahan)->id,
            'kades' => optional($kelurahan)->kades,
            'id_kel' => optional($kelurahan)->id_kel,
            'sekdes' => optional($kelurahan)->sekdes,
            'jumlah_penduduk' => optional($kelurahan)->jumlah_penduduk,
            'luas_daerah' => optional($kelurahan)->luas_daerah,
            'satuan_luas' => optional($kelurahan)->satuan_luas,
            'batas_utara' => optional($kelurahan)->batas_utara,
            'batas_selatan' => optional($kelurahan)->batas_selatan,
            'batas_timur' => optional($kelurahan)->batas_timur,
            'batas_barat' => optional($kelurahan)->batas_barat,
            'lat' => optional($kelurahan)->lat,
            'lng' => optional($kelurahan)->long,
            'zoom' => optional($kelurahan)->zoom,
        ];
    }
}
