<?php

namespace App\Transformers;

use App\Models\Jabatan;
use League\Fractal\TransformerAbstract;

class JabatanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Jabatan $itemJabatan)
    {
        return [
            'id' => $itemJabatan->id,
            'jabatan' => $itemJabatan->jabatan
        ];
    }
}
