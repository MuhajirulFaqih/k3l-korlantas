<?php

namespace App\Transformers;

use App\Models\Kegiatan;
use League\Fractal\TransformerAbstract;

class ExportLaporanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    protected $defaultIncludes = ['user'];

    public function transform(Kegiatan $kegiatan)
    {
        return [
            "id" => $kegiatan->id,
            "user" => $kegiatan->user->pemilik->nama,
            "lat" => $kegiatan->lat,
            "long" => $kegiatan->long,
            "wKegiatan" => $kegiatan->waktu_kegiatan->toDateTimeString(),
            "jenis" => optional($kegiatan->jenis)->jenis,
            "judul" => $kegiatan->judul,
            "uraian" => $kegiatan->uraian,
            "dokumentasi" => url('api/upload/' . $kegiatan->dokumentasi)
        ];
    }

    public function includeUser(Kegiatan $kegiatan)
    {
        return $this->item($kegiatan->user, new UserTransformer());
    }
}
