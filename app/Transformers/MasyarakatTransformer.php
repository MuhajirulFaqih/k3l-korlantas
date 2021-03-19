<?php

namespace App\Transformers;

use App\Models\Masyarakat;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\TransformerAbstract;

class MasyarakatTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kelurahan'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Masyarakat $masyarakat)
    {
        return [
            'id'        => $masyarakat->id,
            'nik'      => $masyarakat->nik,
            'nama'      => $masyarakat->nama,
            'alamat'    => $masyarakat->alamat,
            'no_telp'   => $masyarakat->no_telp,
            'foto'      => $masyarakat->foto ? url('api/upload/'.$masyarakat->foto): null,
            'on_going' => optional($masyarakat->auth)->onGoing()
        ];
    }

    public function includeKelurahan(Masyarakat $masyarakat){
        return $this->item($masyarakat->kelurahan, function($kelurahan) {
            
            $data = fractal($kelurahan)
                    ->parseIncludes(['profil', 'kecamatan'])
                    ->transformWith(new KelurahanTransformer())
                    ->serializeWith(new DataArraySansIncludeSerializer)
                    ->toArray();

            return $data['data'];
        });
    }
}
