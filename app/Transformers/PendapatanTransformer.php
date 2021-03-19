<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Pendapatan;

class PendapatanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['kelurahan'];

    public function transform(Pendapatan $pendapatan)
    {
        return [
            'id' => $pendapatan->id,
            'id_deskel' => $pendapatan->id_kel,
            'hasil_pajak' => $pendapatan->bagihasilpajakDaerah,
            'pendapatan_asli' => $pendapatan->pendapatanaslidaerah,
            'dana_desa' => $pendapatan->alokasidanaDesa,
            'silpa' => $pendapatan->silpa,
            'tahun_anggaran' => $pendapatan->tahun_anggaran,
            'bagihasilpajakDaerah' => $pendapatan->bagihasilpajakDaerah,
            'pendapatanaslidaerah' => $pendapatan->pendapatanaslidaerah,
            'alokasidanaDesa' => $pendapatan->alokasidanaDesa,
        ];
    }

    public function includeKelurahan(Pendapatan $pendapatan)
    {
        return $this->item($pendapatan->kelurahan, new KelurahanTransformer());
    }
}
