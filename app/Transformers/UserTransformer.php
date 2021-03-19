<?php

namespace App\Transformers;

use App\Models\Personil;
use League\Fractal\TransformerAbstract;
use App\Models\User;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    public function transform(User $user)
    {
        switch ($user->jenis_pemilik){
            case 'personil':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->pangkat->pangkat." ".$user->pemilik->nama,
                    'nrp'  => $user->pemilik->nrp,
                    'jabatan'  => $user->pemilik->jabatan->jabatan,
                    'alamat' => $user->pemilik->alamat,
                    'no_telp' => $user->pemilik->no_telp,
                    'id_personil'  => $user->pemilik->id,
                ];
            case 'bhabin':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->personil->pangkat->pangkat." ".$user->pemilik->personil->nama,
                    'nrp'  => $user->pemilik->personil->nrp,
                    'jabatan'  => $user->pemilik->personil->jabatan->jabatan,
                    'alamat' => $user->pemilik->personil->alamat,
                    'no_telp' => $user->pemilik->personil->no_telp,
                    'id_personil'  => $user->pemilik->personil->id,
                ];
            case 'masyarakat':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->nama,
                    'nrp'  => null,
                    'jabatan'  => 'Masyarakat',
                    'alamat' => $user->pemilik->alamat,
                    'no_telp' => $user->pemilik->no_telp,
                    'id_personil'  => null,
                ];
            case 'admin':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->nama,
                    'nrp'  => null,
                    'jabatan'  => 'Root',
                    'alamat' => null,
                    'no_telp' => null,
                    'id_personil'  => null,
                ];
            default:
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => null,
                    'nrp'  => null,
                    'jabatan'  => null,
                    'alamat' => null,
                    'no_telp' => null,
                    'id_personil'  => null,
                ];
        }
    }
}
