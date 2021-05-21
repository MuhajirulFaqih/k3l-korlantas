<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PengumumanTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'kesatuan', 'user'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($pengumuman)
    {
        return [
            'id' => $pengumuman->id,
            'id_user' => $pengumuman->id_user,
            'id_kesatuan' => $pengumuman->id_kesatuan,
            'judul' => $pengumuman->judul,
            'file' => $pengumuman->file ? url('api/upload/'.$pengumuman->file) : null,
            'created_at' => $pengumuman->created_at->toDateTimeString()
        ];
    }

    public function includeKesatuan($pengumuman){
        return $this->item($pengumuman->kesatuan, new KesatuanTransformer());
    }

    public function includeUser($pengumuman){
        return $this->item($pengumuman->user, new UserTransformer());
    }
}
