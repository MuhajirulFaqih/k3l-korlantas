<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Sispammako;

class SispammakoTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Sispammako $itemSispammako)
    {
        return [
            'id' => $itemSispammako->id,
            'pengirim' => $itemSispammako->user->jenis_pemilik != "admin" ? $itemSispammako->user->pemilik->jabatan->jabatan : $itemSispammako->user->pemilik->nama,
            'jenis' => $itemSispammako->jenis,
            'arahan' => $itemSispammako->arahan,
            'dokumen' => url('api/upload/'.$itemSispammako->dokumen),
            'created_at' => $itemSispammako->created_at->toDateTimeString()
        ];
    }
}
