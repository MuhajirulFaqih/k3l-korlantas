<?php

namespace App\Transformers;

use App\Models\JenisKegiatan;
use League\Fractal\TransformerAbstract;

class KegiatanJenisTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'parent'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['children'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($jenisKegiatan)
    {
        return [
            'id' => $jenisKegiatan->id,
            'jenis' => $jenisKegiatan->jenis,
            'jenis_singkat' => $jenisKegiatan->jenis_singkat,
            'keterangan' => $jenisKegiatan->keterangan,
            'has_daftar_rekan' => $jenisKegiatan->has_daftar_rekan,
            'has_nomor_polisi' => $jenisKegiatan->has_nomor_polisi,
            'has_kelurahan' => $jenisKegiatan->has_kelurahan,
            'has_rute' => $jenisKegiatan->has_rute,
        ];
    }

    public function includeParent($jenisKegiatan){
        if ($jenisKegiatan->parent)
            return $this->item($jenisKegiatan->parent, new KegiatanJenisTransformer());
        return null;
    }

    public function includeChildren($jenisKegiatan){
        if($jenisKegiatan->children)
            return $this->collection($jenisKegiatan->children, new KegiatanJenisTransformer());
        return null;
    }
}