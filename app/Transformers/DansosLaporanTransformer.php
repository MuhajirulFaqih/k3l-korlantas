<?php

namespace App\Transformers;

use App\Models\DansosLaporan;
use League\Fractal\TransformerAbstract;

class DansosLaporanTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kelurahan'];

    /**
     * A Fractal transformer.
     *
     * @param DansosLaporan $itemDansosLaporan
     * @return array
     */
    public function transform(DansosLaporan $itemDansosLaporan)
    {
        return [
            'id' => $itemDansosLaporan->id,
            'jumlah' => $itemDansosLaporan->jumlah,
            'kegunaan' => $itemDansosLaporan->kegunaan,
            'kepada' => $itemDansosLaporan->kepada,
            'foto' => $itemDansosLaporan->foto ? url('api/upload/'.$itemDansosLaporan->foto) : null,
            'tahun_anggaran' => $itemDansosLaporan->tahun_anggaran
        ];
    }

    public function includeKelurahan(DansosLaporan $itemDansosLaporan){
        return $this->item($itemDansosLaporan->kelurahan, new KelurahanTransformer());
    }
}
