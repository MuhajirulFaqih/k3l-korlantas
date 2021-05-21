<?php

namespace App\Transformers;

use App\Models\HTChannels;
use League\Fractal\TransformerAbstract;

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
