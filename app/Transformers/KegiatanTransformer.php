<?php

namespace App\Transformers;

use App\Models\JenisKegiatan;
use App\Models\Kegiatan;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class KegiatanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['user'];
    protected $availableIncludes = ['jenis', 'kelurahan'];

    protected $prev = false;

    public function __construct($prev = false)
    {
        $this->$prev = $prev;
    }

    public function transform($kegiatan)
    {
        return [
            'id' => $kegiatan->id,
            'w_kegiatan' => $kegiatan->waktu_kegiatan->toDateTimeString(),
            'daftar_rekan' => $kegiatan->daftar_rekan,
            'nomor_polisi' => $kegiatan->nomor_polisi,
            'detail' => $this->prev ? Str::limit($kegiatan->detail) : $kegiatan->detail,
            'rute_patroli' => $kegiatan->rute_patroli,
            'lat' => $kegiatan->lat,
            'lng' => $kegiatan->lng,
            'is_quick_response' => $kegiatan->is_quick_response,
            'dokumentasi' => $kegiatan->dokumentasi ? url('api/upload/' . $kegiatan->dokumentasi) : null,
        ];
    }

    public function includeUser($kegiatan)
    {
        return $this->item($kegiatan->user, new UserTransformer());
    }

    public function includeJenis($kegiatan){
        if($kegiatan->jenis)
            return $this->collection($kegiatan->jenis, new KegiatanJenisKegiatanTransformer());
        return null;
    }
    
    public function includeKelurahan($kegiatan){
        if($kegiatan->kelurahan)
            return $this->item($kegiatan->kelurahan, new KelurahanTransformer());
        return null;
    }

}
