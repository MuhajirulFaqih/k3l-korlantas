<?php

namespace App\Transformers;

use App\Models\Jenis;
use League\Fractal\TransformerAbstract;
use App\Transformers\TempatVitalTransformer;

class JenisTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Jenis $jenis)
    {
        return [
            'value' => $jenis->id,
            'text' => $jenis->jenis,
            'icon' => url('api/upload/'.$jenis->icon),
            'icon_monit' => $jenis->icon
        ];
    }
}
