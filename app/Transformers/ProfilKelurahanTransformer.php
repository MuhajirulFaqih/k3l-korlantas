<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProfilKelurahan;

class ProfilKelurahanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ProfilKelurahan $itemProfilKelurahan)
    {
        return [
            'id' => $itemProfilKelurahan->id,
            'id_kel' => $itemProfilKelurahan->id_kel,
            'kades' => $itemProfilKelurahan->kades,
            'sekdes' => $itemProfilKelurahan->sekdes,
            'luas_daerah' => $itemProfilKelurahan->luas_daerah,
            'satuan_luas' => $itemProfilKelurahan->satuan_luas,
            'jumlah_penduduk' => $itemProfilKelurahan->jumlah_penduduk,
            'batas_utara' => $itemProfilKelurahan->batas_utara,
            'batas_selatan' => $itemProfilKelurahan->batas_selatan,
            'batas_barat' => $itemProfilKelurahan->batas_barat,
            'batas_timur' => $itemProfilKelurahan->batas_timur,
            'lat' => (double) $itemProfilKelurahan->lat,
            'lng' => (double) $itemProfilKelurahan->long,
            'zoom' => (int) $itemProfilKelurahan->zoom
        ];
    }

    public function includeKelurahan(ProfilKelurahan $itemProfilKelurahan){
        return $this->item($itemProfilKelurahan->kelurahan, new KelurahanTransformer());
    }
}
