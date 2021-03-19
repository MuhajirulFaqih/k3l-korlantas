<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Komentar;

class KomentarTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    // protected $defaultIncludes = ['user'];
    
    public function transform(Komentar $komentar)
    {
        return [
            'id'       => $komentar->id,
            'id_induk' => $komentar->id_induk,
            'komentar' => $komentar->komentar,
            'id_user'  => $komentar->id_user,
            'w_komentar' => $komentar->created_at->toDateTimeString(),
            'induk' => $komentar->induk->judul ?? $komentar->induk->kejadian,
        ];
    }

     public function includeUser(Komentar $komentar)
     {
         return $this->item($komentar->user, new UserTransformer);
     }

}