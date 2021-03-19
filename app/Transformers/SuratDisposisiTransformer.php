<?php

namespace App\Transformers;

use App\Models\SuratDisposisi;
use League\Fractal\TransformerAbstract;

class SuratDisposisiTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['jabatan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(SuratDisposisi $itemSuratDisposisi)
    {
        return [
            'id' => $itemSuratDisposisi->id,
            'id_surat' => $itemSuratDisposisi->id_surat,
            'w_diterima' => $itemSuratDisposisi->w_diterima,
            'derajat' => $itemSuratDisposisi->derajat,
            'klasifikasi' => $itemSuratDisposisi->klasifikasi,
            'w_disposisi' => $itemSuratDisposisi->w_disposisi,
            'file' => $itemSuratDisposisi->file ? url('api/upload/'.$itemSuratDisposisi->file) : null,
            'isi' => $itemSuratDisposisi->isi,
            'created_at' => $itemSuratDisposisi->created_at->toDateTimeString()
        ];
    }

    public function includeSurat(SuratDisposisi $itemDisposisi){
        return $this->item($itemDisposisi->surat, new SuratTransformer());
    }

    public function includeJabatan(SuratDisposisi $itemDisposisi){
        return $this->collection($itemDisposisi->jabatan, new JabatanTransformer());
    }
}
