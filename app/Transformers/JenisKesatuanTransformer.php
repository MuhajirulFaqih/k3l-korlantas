<?php

namespace App\Transformers;

use App\Models\JenisKesatuan;
use League\Fractal\TransformerAbstract;

class JenisKesatuanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(JenisKesatuan $item)
    {
        return [
            'id' => $item->id,
            'jenis' => $item->jenis
        ];
    }
}
