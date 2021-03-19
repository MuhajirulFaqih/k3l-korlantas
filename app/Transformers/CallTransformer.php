<?php

namespace App\Transformers;

use App\Models\CallLog;
use League\Fractal\TransformerAbstract;

class CallTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CallLog $call)
    {
        return [
            'from' => $call->fromData,
            'to' => $call->toData,
            'start' => $call->start,
            'end' => $call->end,
        ];
    }
}
