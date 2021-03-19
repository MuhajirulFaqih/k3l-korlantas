<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\BelanjaBidang;

class BelanjaBidangTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['kelurahan'];

    public function transform(BelanjaBidang $belanja)
    {
        return [
            'id' => $belanja->id,
            'penyelenggaraan' => $belanja->penyelenggaraan,
            'pelaksanaan' => $belanja->pelaksanaan,
            'pemberdayaan' => $belanja->pemberdayaan,
            'pembinaan' => $belanja->pembinaan,
            'tak_terduga' => $belanja->tak_terduga,
            'tahun_anggaran' => $belanja->tahun_anggaran,
        ];
    }

    public function includeKelurahan(BelanjaBidang $belanja)
    {
        return $this->item($belanja->kelurahan, new KelurahanTransformer());
    }
}
