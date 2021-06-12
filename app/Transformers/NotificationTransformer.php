<?php

namespace App\Transformers;

use App\Models\User;
use App\Notifications\GroupCallNotification;
use App\Notifications\KegiatanNotification;
use App\Notifications\KejadianNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
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
        //
    ];

    protected $formatData = [
        KegiatanNotification::class => 'kegiatan',
        KejadianNotification::class => 'kejadian',
        GroupCallNotification::class => 'group-call',
        User::class => 'user'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($item)
    {
        $data = $item->toArray();
        $data['notifiable_type'] = isset($this->formatData[$item->notifiable_type]) ? $this->formatData[$item->notifiable_type] : $item->notifiable_type;
        $data['type'] = isset($this->formatData[$item->type]) ? $this->formatData[$item->type] : $item->type;
        return $data;
    }
}
