<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Jabatan;

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

    public function kesatuan(Jabatan $itemJabatan){
        return $this->item($itemJabatan->kesatuan, new KesatuanTransformer());
    }
}
