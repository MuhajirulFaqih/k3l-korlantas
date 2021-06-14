<?php

namespace App\Transformers;

use App\Services\PersonilService;
use League\Fractal\TransformerAbstract;

class LogStatusDinasTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'patroli'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($itemStatusDinas)
    {
        return [
            'id' => $itemStatusDinas->id,
            'kegiatan' => $itemStatusDinas->status->kegiatan,
            'lama' => $itemStatusDinas->waktu_selesai_dinas ? str_replace(" sebelumnya", "", $itemStatusDinas->waktu_mulai_dinas->diffForHumans($itemStatusDinas->waktu_selesai_dinas)) : "Sedang dilakukan.",
            'jarak' => (new PersonilService())->getDistances($itemStatusDinas->logpatroli),
            'created_at' => $itemStatusDinas->created_at->toDateTimeString()
        ];
    }

    public function includePatroli($itemStatusDinas){
        if ($itemStatusDinas->logpatroli)
            return $this->collection($itemStatusDinas->logpatroli, function ($item) {
                return [
                    'id' => $item->id,
                    'lat' => $item->lat,
                    'lng' => $item->lng
                ];
            });
        return null;
    }
}
