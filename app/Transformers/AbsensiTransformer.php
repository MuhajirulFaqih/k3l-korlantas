<?php

namespace App\Transformers;

use App\Models\Absensi;
use League\Fractal\TransformerAbstract;

class AbsensiTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['personil'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Absensi $absensi)
    {
        return [
            'id' => $absensi->id,
            'waktu_mulai' => $absensi->waktu_mulai,
            'waktu_selesai' => $absensi->waktu_selesai,
        ];
    }

    public function includePersonil(Absensi $absensi)
    {
        return $this->item($absensi->personil, new PersonilTransformer);
    }
}
