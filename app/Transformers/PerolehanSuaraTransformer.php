<?php

namespace App\Transformers;

use App\Models\PerolehanSuara;
use League\Fractal\TransformerAbstract;

class PerolehanSuaraTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['paslon'];

    /**
     * A Fractal transformer.
     *
     * @param PerolehanSuara $itemPerolehanSuara
     * @return array
     */
    public function transform(PerolehanSuara $itemPerolehanSuara)
    {
        return [
            'id' => $itemPerolehanSuara->id,
            'suara' => $itemPerolehanSuara->suara,
        ];
    }

    public function includePaslon(PerolehanSuara $itemPerolehanSuara){
        return $this->item($itemPerolehanSuara->paslon, PaslonTransformer::class);
    }

    public function includeTps(PerolehanSuara $itemPerolehanSuara){
        return $this->item($itemPerolehanSuara->tps, TpsBupTransformer::class);
    }
}
