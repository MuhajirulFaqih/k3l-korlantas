<?php

namespace App\Transformers;

use App\Models\LogPosisiMasyarakat;
use League\Fractal\TransformerAbstract;

class LogMasyarakatTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user', 'induk'];
    /**
     * A Fractal transformer.
     *
     * @param LogPosisiMasyarakat $itemLogMasyarakat
     * @return array
     */
    public function transform(LogPosisiMasyarakat $itemLogMasyarakat)
    {
        return [
            'id' => $itemLogMasyarakat->id,
            'lat' => $itemLogMasyarakat->lat,
            'lng' => $itemLogMasyarakat->lng,
            'jenis_induk' => $itemLogMasyarakat->jenis_induk,
        ];
    }


    public function includeUser(LogPosisiMasyarakat $itemLogMasyarakat){
        return $this->item($itemLogMasyarakat->user, new UserTransformer());
    }

    public function includeInduk(LogPosisiMasyarakat $itemLogMasyarakat){
        return $this->item($itemLogMasyarakat->induk, $itemLogMasyarakat->jenis_induk == 'darurat' ? new DaruratTransformer() : new KejadianTransformer());
    }
}
