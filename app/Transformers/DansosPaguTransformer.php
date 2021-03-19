<?php

namespace App\Transformers;

use App\Models\DansosPagu;
use League\Fractal\TransformerAbstract;

class DansosPaguTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kelurahan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(DansosPagu $itemDansosPagu)
    {
        return [
            'id' => $itemDansosPagu->id,
            'jumlah' => $itemDansosPagu->pagu,
            'asal' => $itemDansosPagu->asal,
            'tahun_anggaran' => $itemDansosPagu->tahun_anggaran
        ];
    }

    public function includeKelurahan(DansosPagu $itemDansosPagu){
        return $this->item($itemDansosPagu->kelurahan, new KelurahanTransformer());
    }
}
