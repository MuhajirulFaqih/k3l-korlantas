<?php

namespace App\Transformers;

use App\Models\Kabupaten;
use League\Fractal\TransformerAbstract;

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
