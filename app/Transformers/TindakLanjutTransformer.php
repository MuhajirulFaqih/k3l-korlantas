<?php

namespace App\Transformers;

use App\Models\TindakLanjut;
use League\Fractal\TransformerAbstract;

class TindakLanjutTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];

    protected $availableIncludes = ['kejadian'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TindakLanjut $itemTindakLanjut)
    {
        return [
            'id' => $itemTindakLanjut->id,
            'id_kejadian' => $itemTindakLanjut->id_kejadian,
            'id_user' => $itemTindakLanjut->id_user,
            'status' => $itemTindakLanjut->status,
            'status_readable' => str_replace('_', ' ', title_case($itemTindakLanjut->status)),
            'keterangan' => $itemTindakLanjut->keterangan,
            'judul' => $itemTindakLanjut->kejadian->kejadian,
            'foto' => $itemTindakLanjut->foto ? url('api/upload/'.$itemTindakLanjut->foto): null,
            'created_at' => $itemTindakLanjut->created_at->toDateTimeString()
        ];
    }

    public function includeUser(TindakLanjut $itemTindakLanjut){
        return $this->item($itemTindakLanjut->user, new UserTransformer());
    }


    public function includeKejadian(TindakLanjut $itemTindakLanjut){
        return $this->item($itemTindakLanjut->kejadian, new KejadianTransformer());
    }
}
