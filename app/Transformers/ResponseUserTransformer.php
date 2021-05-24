<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class ResponseUserTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['pemilik'];

    protected $availableIncludes = ['pemilik', 'personil'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        $return = [
            'id' => $user->id,
            'username' => $user->username,
            'diverifikasi' => $user->diverifikasi,
            'id_pemilik' => $user->id_pemilik,
            'jenis_pemilik' => $user->jenis_pemilik
        ];
        return $return;
    }

    public function includePemilik(User $user){
        switch ($user->jenis_pemilik) {
            case 'personil':
                $pemilik = new PersonilTransformer();
                break;
            case 'admin':
                $pemilik = new AdminTransformer();
                break;
            case 'kesatuan':
                $pemilik = new KesatuanTransformer();
                break;
            default:
                $pemilik = new MasyarakatTransformer();
                break;
        }
        return $this->item($user->pemilik, $pemilik);
    }
}
