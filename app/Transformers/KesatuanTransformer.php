<?php

namespace App\Transformers;

use App\Models\Kesatuan;
use League\Fractal\TransformerAbstract;

class KesatuanTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['jenis'];
    protected $availableIncludes = ['parent', 'children', 'auth'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(object $itemKesatuan)
    {
        return [
            'id' => $itemKesatuan->id,
            'kesatuan' => $itemKesatuan->kesatuan,
            'kesatuan_lengkap' => in_array($itemKesatuan->level, [1, 2]) ? $itemKesatuan->kesatuan : $itemKesatuan->kesatuan .' '.$itemKesatuan->parent->kesatuan
        ];
    }

    public function includeAuth($itemKesatuan){
        if ($itemKesatuan->auth){
            return $this->item($itemKesatuan->auth, function ($itemAuth) {
               return [
                   'id' => $itemAuth->id,
                   'username' => $itemAuth->username,
                   'online' => $itemAuth->online
               ];
            });
        }
        return null;
    }

    public function includeParent(object $itemKesatuan){
        if ($itemKesatuan->parent)
            return $this->item($itemKesatuan->parent, new KesatuanTransformer());
        return null;
    }

    public function includeChildren(object $itemKesatuan){
        if ($itemKesatuan->children)
            return $this->collection($itemKesatuan->children, new KesatuanTransformer());
        return collect();
    }

    public function includeJenis(object $itemKesatuan){
        if ($itemKesatuan->jenis)
            return $this->item($itemKesatuan->jenis, new JenisKesatuanTransformer());
        return null;
    }
}
