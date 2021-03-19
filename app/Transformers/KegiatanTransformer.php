<?php

/**
 * @Author: Alan
 * @Date:   2018-09-25 10:23:03
 * @Last Modified by:   Alan
 * @Last Modified time: 2018-09-27 10:44:48
 */

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Kegiatan;
use App\Models\Komentar;
use App\Models\Personil;
use App\Models\TipeLaporan;
use App\Models\JenisKegiatan;

class KegiatanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['user'];

    protected $availableIncludes = ['tipe', 'jenis'];

    protected $prev = false;

    public function __construct($prev = false)
    {
        $this->$prev = $prev;
    }

    public function transform(Kegiatan $kegiatan)
    {
        return [
            'id' => $kegiatan->id,
            'w_kegiatan' => $kegiatan->waktu_kegiatan->toDateTimeString(),
            'judul' => $kegiatan->judul,
            // 'tipe'          => $kegiatan->tipe->tipe,
            // 'jenis'         => optional($kegiatan->jenis)->jenis,
            'lat' => $kegiatan->lat,
            'lng' => $kegiatan->lng,
            'lokasi' => $kegiatan->lokasi,
            'sasaran' => $kegiatan->sasaran,
            'kuat_pers' => $kegiatan->kuat_pers,
            'hasil' => $kegiatan->hasil,
            'jml_giat' => $kegiatan->jml_giat,
            'jml_tsk' => $kegiatan->jml_tsk,
            'bb' => $kegiatan->jml_tsk,
            'perkembangan' => $kegiatan->perkembangan,
            'dasar' => $kegiatan->dasar,
            'keterangan' => $this->prev ? str_limit($kegiatan->keterangan) : $kegiatan->keterangan,
            'dokumentasi' => $kegiatan->dokumentasi ? url('api/upload/' . $kegiatan->dokumentasi) : null,
        ];
    }

    public function includeUser(Kegiatan $kegiatan)
    {
        return $this->item($kegiatan->user, new UserTransformer());
    }

    public function includeTipe(Kegiatan $kegiatan)
    {
        return $this->item($kegiatan->tipe, function (TipeLaporan $tipeLaporan) {
            return $tipeLaporan->toArray();
        });
    }

    public function includeJenis(Kegiatan $kegiatan)
    {
        if ($kegiatan->jenis)
            return $this->item($kegiatan->jenis, function (JenisKegiatan $jenisKegiatan) {
            return $jenisKegiatan->toArray();
        });
    }

}
