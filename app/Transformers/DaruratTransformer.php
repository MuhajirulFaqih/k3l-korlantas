<?php

namespace App\Transformers;

use App\Models\Darurat;
use League\Fractal\TransformerAbstract;

class DaruratTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user', 'nearby'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Darurat $daruratItem)
    {
        return [
            'id' => $daruratItem->id,
            'id_user' => $daruratItem->id_user,
            'lat' => $daruratItem->lat,
            'lng' => $daruratItem->lng,
            'acc' => $daruratItem->acc,
            'selesai' => $daruratItem->selesai,
            'kejadian' => $daruratItem->kejadian ?? null,
            'created_at' => optional($daruratItem->created_at)->toDateTimeString(),
        ];
    }

    public function includeUser(Darurat $daruratItem){
        return $this->item($daruratItem->user, new UserTransformer);
    }

    public function includeNearby(Darurat $daruratItem){
        return $this->collection($daruratItem->nearby, new PersonilTerdekatTransformer);
    }
}
