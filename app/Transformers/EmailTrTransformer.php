<?php

namespace App\Transformers;

use App\Models\EmailTr;
use League\Fractal\TransformerAbstract;

class EmailTrTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['attachment'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(EmailTr $itemEmailTr)
    {
        return [
            'id' => $itemEmailTr->id,
            'nomor' => $itemEmailTr->nomor,
            'pengirim' => $itemEmailTr->pengirim,
            'w_email' => $itemEmailTr->w_email,
            'id_email' => $itemEmailTr->id_email
        ];
    }


    public function includeAttachment(EmailTr $itemEmailTr){
        return $this->collection($itemEmailTr->attachment, function ($itemAttachment){
            return [
                'id' => $itemAttachment->id,
                'id_email' => $itemAttachment->id_email,
                'nama' => $itemAttachment->nama,
                'file' => url('api/upload/'.$itemAttachment->file),
                'format' => $itemAttachment->format
            ];
        });
    }
}
