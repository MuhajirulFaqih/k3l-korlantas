<?php

namespace App\Transformers;

use App\Models\Plb;
use League\Fractal\TransformerAbstract;

class PlbTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user', 'tujuan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Plb $plb)
    {
        return [
            'id' => $plb->id,
            'id_user' => $plb->id_user,
            'lat' => $plb->lat,
            'lng' => $plb->lng,
            'keterangan' => $plb->keterangan,
            'created_at' => optional($plb->created_at)->toDateTimeString(),
        ];
    }

    public function includeUser(Plb $plb){
        return $this->item($plb->user, new UserTransformer);
    }
    
    public function includeTujuan(Plb $plb){
        return $this->item($plb->tujuan, new KesatuanTransformer);
    }
}
