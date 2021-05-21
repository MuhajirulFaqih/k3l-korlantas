<?php

namespace App\Transformers;

use App\Models\Kelurahan;
use League\Fractal\TransformerAbstract;

class KelurahanTransformer extends TransformerAbstract
{
    //protected $defaultIncludes = ['kecamatan'];

    protected $availableIncludes = ['kecamatan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Kelurahan $itemKelurahan)
    {
        return [
            'id_kel' => $itemKelurahan->id_kel,
            'id_kec' => $itemKelurahan->id_kec,
            'nama' => $itemKelurahan->nama,
            'jenis' => $itemKelurahan->jenis
        ];
    }

    public function includeKecamatan(Kelurahan $itemKelurahan){
        return $this->item($itemKelurahan->kecamatan, new KecamatanTransformer());
    }

    /*public function includeProfil(Kelurahan $itemKelurahan){
        if($itemKelurahan->profil) {
            return $this->item($itemKelurahan->profil, new ProfileDesaTransformer());
        }
        return null;
    }*/
}
