<?php

namespace App\Transformers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

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
                    'id_personil' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->pangkat->pangkat." ".$user->pemilik->nama ?? null,
                    'nrp'  => $user->pemilik->nrp,
                    'foto' => Storage::exists("personil/".$user->pemilik->nrp.".jpg") ? url('api/upload/personil/' . $user->pemilik->nrp . '.jpg').'?time='.Storage::lastModified('personil/'.$user->pemilik->nrp.'.jpg') : url('api/upload/personil/presisi.jpg'),
                    'jabatan'  => $user->pemilik->jabatan->jabatan ?? null,
                    'alamat' => $user->pemilik->alamat,
                    'no_telp' => $user->pemilik->no_telp,
                    'id_personil'  => $user->pemilik->id,
                ];
            case 'masyarakat':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->nama,
                    'foto'      => $user->pemilik->foto ? url('api/upload/'.$user->pemilik->foto): null,
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
                    'username' => $user->username,
                    'nrp'  => null,
                    'jabatan'  => 'Admin',
                    'alamat' => null,
                    'no_telp' => null,
                    'id_personil'  => null,
                ];
            case 'kesatuan':
                return [
                    'id' => $user->id,
                    'id_pemilik' => $user->id_pemilik,
                    'jenis_pemilik' => $user->jenis_pemilik,
                    'nama'     => $user->pemilik->kesatuan,
                    'username' => $user->username,
                    'nrp'  => null,
                    'jabatan'  => 'Kesatuan',
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
