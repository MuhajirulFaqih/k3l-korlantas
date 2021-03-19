<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\HTChannels;

class HTChannelsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(HTChannels $ht)
    {
        return [
            'channel_id' => $ht->channel_id,
            'used_in' => $ht->used_in,
            'name' => $ht->name
        ];
    }
}
