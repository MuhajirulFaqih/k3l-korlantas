<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Kelurahan;

class BhabinKelurahanTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['profil', 'kecamatan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Kelurahan $kelurahan)
    {
        return [
            'id_kel' => $kelurahan->id_kel,
            'id_kec' => $kelurahan->id_kec,
            'nama' => $kelurahan->nama,
            'jenis' => $kelurahan->jenis,
        ];
    }

    public function includeProfil(Kelurahan $kelurahan){
        if ($kelurahan->profil)
            return $this->item($kelurahan->profil, new ProfilKelurahanTransformer());
    }

    public function includeKecamatan(Kelurahan $kelurahan){
        return $this->item($kelurahan->kecamatan, new KecamatanTransformer());
    }
}
