<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\GiatDanaDesa;

class GiatDanaDesaTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['kelurahan'];

    public function transform(GiatDanaDesa $giat)
    {
        return [
            'id' => $giat->id,
            'giat' => $giat->giat,
            'keterangan' => $giat->keterangan,
            'biaya' => $giat->biaya,
            'foto' => url('api/upload/' . $giat->foto),
            'created_at' => $giat->created_at->toDateTimeString()
        ];
    }

    public function includeKelurahan(GiatDanaDesa $giat)
    {
        return $this->item($giat->kelurahan, new KelurahanTransformer());
    }
}
