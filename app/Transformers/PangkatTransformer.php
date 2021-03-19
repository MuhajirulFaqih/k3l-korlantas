<?php

namespace App\Transformers;

use App\Models\Pangkat;
use League\Fractal\TransformerAbstract;
use App\Models\User;

class PangkatTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Pangkat $pangkat)
    {
        return [
            'id'                => $pangkat->id,
            'pangkat'           => $pangkat->pangkat,
            'pangkat_lengkap'   => $pangkat->pangkat_lengkap
        ];
    }
}
