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

        if (in_array($user->jenis_pemilik, ['bhabin', 'personil'])){
            $return['pamtps'] = count($user->jenis_pemilik == 'personil' ? $user->pemilik->pamtps : $user->pemilik->personil->pamtps) > 0;
            $return['hitung_open'] = config('pengaturan.hitungpilpres') == 1;

            if (env("VISI_MISI")){
                $return['visi_misi'] = url('api/upload/'.config("pengaturan.pdf_visi_misi"));
                $return['program_kapolres'] = url('api/upload/'.config("pengaturan.pdf_program_kapolres"));
                $return['kebijakan_kapolres'] = url('api/upload/'.config("pengaturan.pdf_kebijakan_kapolres"));
            }
        }

        return $return;
    }

    public function includePemilik(User $user){
        switch ($user->jenis_pemilik) {
            case 'personil':
                $pemilik = new PersonilTransformer();
                break;
            case 'bhabin':
                $pemilik = new BhabinTransformer();
                break;
            case 'admin': 
                $pemilik = new AdminTransformer();
                break;
            default:
                $pemilik = new MasyarakatTransformer();
                break;
        }
        return $this->item($user->pemilik, $pemilik);
    }
}
