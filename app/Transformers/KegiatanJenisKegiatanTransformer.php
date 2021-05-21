<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class KegiatanJenisKegiatanTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [ 'jenis' ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [  ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($kegiatanJenisKegiatan)
    {
        return [
            'id' => $kegiatanJenisKegiatan->id,
            'id_kegiatan' => $kegiatanJenisKegiatan->id_kegiatan,
            'id_jenis_kegiatan' => $kegiatanJenisKegiatan->id_jenis_kegiatan
        ];
    }

    public function includeJenis($kegiatanJenisKegiatan){
        if($kegiatanJenisKegiatan->jenis_kegiatan)
            return $this->item($kegiatanJenisKegiatan->jenis_kegiatan, new KegiatanJenisTransformer());
    }
}
