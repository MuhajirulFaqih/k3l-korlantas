<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Bhabin;

class BhabinTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['kelurahan'];

    protected $availableIncludes = ['personil'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Bhabin $itemBhabin)
    {
        return [
            'id_personil' => $itemBhabin->id_personil
        ];
    }

    public function includePersonil(Bhabin $itemBhabin)
    {
        return $this->item($itemBhabin->personil, new PersonilTransformer());
    }

    public function includeKelurahan(Bhabin $itemBhabin)
    {
        return $this->collection($itemBhabin->kelurahan, function ($itemKelurahan) {
            return [
                'id_kel' => $itemKelurahan->id_kel,
                'id_kec' => $itemKelurahan->id_kec,
                'nama' => $itemKelurahan->nama
            ];
        });
    }
}
