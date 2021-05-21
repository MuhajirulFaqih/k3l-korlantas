<?php

namespace App\Transformers;

use App\Models\Dinas;
use League\Fractal\TransformerAbstract;

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
            'icon'     => $dinas->icon ? url('api/upload/'.$dinas->icon) : null,
            'image' => $dinas->image ? url('api/upload/'.$dinas->image) : null
        ];
    }
}
