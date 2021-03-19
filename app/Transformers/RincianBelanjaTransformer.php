<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\RincianBelanja;

class RincianBelanjaTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['kelurahan'];

    public function transform(RincianBelanja $rincianbelanja)
    {
        return [
            'id' => $rincianbelanja->id,
            'uraian' => $rincianbelanja->uraian,
            'jumlah' => $rincianbelanja->jumlah,
            'tahun_anggaran' => $rincianbelanja->tahun_anggaran,
        ];
    }

    public function includeKelurahan(RincianBelanja $rincianbelanja)
    {
        return $this->item($rincianbelanja->kelurahan, new KelurahanTransformer());
    }
}
