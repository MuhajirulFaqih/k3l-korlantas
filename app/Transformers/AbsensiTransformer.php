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
            'lat_datang' => $absensi->lat_datang,
            'lat_pulang' => $absensi->lat_pulang,
            'lng_datang' => $absensi->lng_datang,
            'lng_pulang' => $absensi->lng_pulang,
            'lokasi_datang' => $absensi->lokasi_datang,
            'lokasi_pulang' => $absensi->lokasi_pulang,
            'kondisi_datang' => $absensi->kondisi_datang,
            'kondisi_pulang' => $absensi->kondisi_pulang,
            'dokumentasi_datang' => $absensi->dokumentasi_datang ? url('api/upload/' . $absensi->dokumentasi_datang) : null,
            'dokumentasi_pulang' => $absensi->dokumentasi_pulang ? url('api/upload/' . $absensi->dokumentasi_pulang) : null,
        ];
    }

    public function includePersonil(Absensi $absensi)
    {
        return $this->item($absensi->personil, new PersonilTransformer);
    }
}
