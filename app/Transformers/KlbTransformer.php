<?php

namespace App\Transformers;

use App\Models\Klb;
use League\Fractal\TransformerAbstract;

class KlbTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Klb $klb)
    {
        return [
            'id' => $klb->id,
            'id_user' => $klb->id_user,
            'lat' => $klb->lat,
            'lng' => $klb->lng,
            'keterangan' => $klb->keterangan,
            'created_at' => optional($klb->created_at)->toDateTimeString(),
        ];
    }

    public function includeUser(Klb $klb){
        return $this->item($klb->user, new UserTransformer);
    }
}
