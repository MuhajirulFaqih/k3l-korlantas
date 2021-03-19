<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Kesatuan;

class KesatuanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Kesatuan $itemKesatuan)
    {
        return [
            'id' => $itemKesatuan->id,
            'kesatuan' => $itemKesatuan->kesatuan,
            'induk' => $itemKesatuan->induk,
            'email' => $itemKesatuan->email_polri,
            'icon' => $itemKesatuan->icon ? url('upload/' . $itemKesatuan->icon) : null,
            'banner' => $itemKesatuan->banner_grid ? url('api/upload/'.$itemKesatuan->banner_grid) : null
        ];
    }
}
