<?php

namespace App\Transformers;

use App\Models\BukuSaku;
use League\Fractal\TransformerAbstract;

class BukuSakuTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param BukuSaku $itemBukuSaku
     * @return array
     */
    public function transform(BukuSaku $itemBukuSaku)
    {
        return [
            'id' => $itemBukuSaku->id,
            'judul' => $itemBukuSaku->judul,
            'file' => url('api/upload/'.$itemBukuSaku->file)
        ];
    }
}
