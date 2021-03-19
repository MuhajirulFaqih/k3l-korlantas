<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Kegiatan;

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
            "tipeLaporan" => $kegiatan->tipe_laporan,
            "lat" => $kegiatan->lat,
            "long" => $kegiatan->long,
            "wKegiatan" => $kegiatan->waktu_kegiatan,
            "jenis_kegiatan" => $kegiatan->jenis_kegiatan,
            "jenis" => optional($kegiatan->jenis)->jenis,
            "judul" => $kegiatan->judul,
            "sasaran" => $kegiatan->sasaran,
            "lokasi" => $kegiatan->lokasi,
            "kuatPers" => $kegiatan->kuat_pers,
            "hasil" => $kegiatan->hasil,
            "jmlGiat" => $kegiatan->jml_giat,
            "jmlTsk" => $kegiatan->jml_tsk,
            "bb" => $kegiatan->bb,
            "perkembangan" => $kegiatan->perkembangan,
            "dasar" => $kegiatan->dasar,
            "modus" => $kegiatan->modus,
            "tsk_bb" => $kegiatan->tsk_bb,
            "keterangan" => $kegiatan->keterangan,
            "dokumentasi" => url('api/upload/' . $kegiatan->dokumentasi)
        ];
    }
    
    public function includeUser(Kegiatan $kegiatan)
    {
        return $this->item($kegiatan->user, new UserTransformer());
    }
}
