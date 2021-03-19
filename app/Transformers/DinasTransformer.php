<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Dinas;

class DinasTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Dinas $dinas)
    {
        return [
            'id'       => $dinas->id,
            'kegiatan' => $dinas->kegiatan,
            'icon'     => $dinas->icon ? url('api/upload/'.$dinas->icon) : null
        ];
    }
}
