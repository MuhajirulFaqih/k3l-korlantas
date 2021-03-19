<?php

namespace App\Transformers;

use App\Models\PersonilTerdekat;
use League\Fractal\TransformerAbstract;

class PersonilTerdekatTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['personil'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PersonilTerdekat $personilTerdekat)
    {
        return [
            'id' => $personilTerdekat->id,
            'id_personil' => $personilTerdekat->id_personil,
            'lat' => $personilTerdekat->lat,
            'lng' => $personilTerdekat->lng,
            'created_at' => $personilTerdekat->created_at->toDateTimeString()
        ];
    }

    public function includePersonil(PersonilTerdekat $personilTerdekat){
        return $this->item($personilTerdekat->personil, new PersonilTransformer());
    }
}
