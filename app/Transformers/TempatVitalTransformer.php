<?php

namespace App\Transformers;

use App\Models\TempatVital;
use App\Transformers\JenisTransformer;
use League\Fractal\TransformerAbstract;

class TempatVitalTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['jenis'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TempatVital $tempat)
    {
        return [
            'id' => $tempat->id,
            'nama_tempat' => $tempat->nama_tempat,
            'lokasi' => $tempat->lokasi,
            'lat' => $tempat->lat,
            'lng' => $tempat->lng,
            'jajaran' => $tempat->jajaran
        ];
    }

    public function includeJenis(TempatVital $tempat)
    {
        return $this->item($tempat->jenis, new JenisTransformer());
    }
}
