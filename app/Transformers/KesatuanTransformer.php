<?php

namespace App\Transformers;

use App\Models\Kesatuan;
use League\Fractal\TransformerAbstract;

class KesatuanTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [];
    protected $availableIncludes = ['parent', 'children'];
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
}
