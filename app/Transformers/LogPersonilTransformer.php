<?php

namespace App\Transformers;

use App\Models\LogPersonil;
use App\Transformers\DinasTransformer;
use App\Transformers\PersonilTransformer;
use League\Fractal\TransformerAbstract;

class LogPersonilTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['personil', 'status'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(LogPersonil $log)
    {
        return [
            'id' => $log->id,
            'waktu_mulai_dinas' => $log->waktu_mulai_dinas,
            'waktu_selesai_dinas' => $log->waktu_selesai_dinas,
            'created_at' => $log->created_at->toDateTimeString(),
            'patroliPengawalan' => $log->logpatroli ?? null,
        ];
    }

    public function includePersonil(LogPersonil $log)
    {
        return $this->item($log->personil, new PersonilTransformer);
    }

    public function includeStatus(LogPersonil $log)
    {
        return $this->item($log->status, new DinasTransformer);
    }
}
