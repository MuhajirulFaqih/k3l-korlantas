<?php

namespace App\Transformers;

use App\Models\Kecamatan;
use League\Fractal\TransformerAbstract;

class KecamatanTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['kabupaten'];
    protected $availableIncludes = ['kelurahan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Kecamatan $itemKecamatan)
    {
        return [
            'id_kec' => $itemKecamatan->id_kec,
            'id_kab' => $itemKecamatan->id_kab,
            'nama' => $itemKecamatan->nama
        ];
    }

    public function includeKabupaten(Kecamatan $itemKecamatan){
        return $this->item($itemKecamatan->kabupaten, new KabupatenTransformer());
    }

    public function includeKelurahan(Kecamatan $itemKecamatan){
        return $this->collection($itemKecamatan->kelurahan->sortBy('nama'), new KelurahanTransformer());
    }
}
