<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Informasi;

class InformasiTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Informasi $informasi)
    {
        return [
            'id' => $informasi->id,
            'aktif' => $informasi->aktif,
            'informasi' => $informasi->informasi,
            'created_at' => $informasi->created_at->toDateTimeString()
        ];
    }
}
