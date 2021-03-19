<?php

namespace App\Transformers;

use App\Models\TigaPilar;
use League\Fractal\TransformerAbstract;

class TigaPilarTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kelurahan'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TigaPilar $tigaPilar)
    {
        return [
            'id' => $tigaPilar->id,
            'nama' => $tigaPilar->nama,
            'no_telp' => $tigaPilar->no_telp,
            'jabatan' => $tigaPilar->jabatan,
            'foto' => $tigaPilar->foto ? url('api/upload/'.$tigaPilar->foto) : null,
            'id_deskel' => $tigaPilar->id_deskel,
            'kelurahan' => $tigaPilar->kelurahan->nama,
            'kecamatan' => $tigaPilar->kelurahan->kecamatan->nama
        ];
    }

    public function includeKelurahan(TigaPilar $tigaPilar){
        return $this->item($tigaPilar->kelurahan, function ($kelurahan){
           return [
               'id' => $kelurahan->id_kel,
               'nama' => $kelurahan->nama
           ];
        });
    }
}
