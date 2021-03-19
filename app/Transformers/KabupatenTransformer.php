<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Kabupaten;

class KabupatenTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kecamatan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Kabupaten $itemKabupaten)
    {
        return [
            'id_kab' => $itemKabupaten->id_kab,
            'id_prov' => $itemKabupaten->id_prov,
            'nama' => $itemKabupaten->nama
        ];
    }

    public function includeKecamatan(Kabupaten $itemKabupaten){
        return $this->collection($itemKabupaten->kecamatan, new KecamatanTransformer());
    }
}
