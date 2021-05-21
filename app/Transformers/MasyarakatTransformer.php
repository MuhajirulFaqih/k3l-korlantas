<?php

namespace App\Transformers;

use App\Models\Masyarakat;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\TransformerAbstract;

class MasyarakatTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['kelurahan', 'auth'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Masyarakat $masyarakat)
    {
        return [
            'id'            => $masyarakat->id,
            'nik'           => $masyarakat->nik,
            'nama'          => $masyarakat->nama,
            'alamat'        => $masyarakat->alamat,
            'no_telp'       => $masyarakat->no_telp,
            'foto'          => $masyarakat->foto ? url('api/upload/'.$masyarakat->foto): null,
            'created_at'    => $masyarakat->created_at,
        ];
    }

    public function includeAuth(Masyarakat $masyarakat){
        return $this->item($masyarakat->auth, function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'diverifikasi' => $user->diverifikasi,
                'id_pemilik' => $user->id_pemilik,
                'jenis_pemilik' => $user->jenis_pemilik
            ];
        });
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
