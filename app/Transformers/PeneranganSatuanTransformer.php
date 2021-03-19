<?php

namespace App\Transformers;

use App\Models\PeneranganSatuan;
use App\Models\PeneranganSatuanFile;
use League\Fractal\TransformerAbstract;

class PeneranganSatuanTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['files'];
    /**
     * A Fractal transformer.
     *
     * @param PeneranganSatuan $peneranganSatuan
     * @return array
     */
    public function transform(PeneranganSatuan $peneranganSatuan)
    {
        return [
            'id' => $peneranganSatuan->id,
            'judul' => $peneranganSatuan->judul,
            'keterangan' => $peneranganSatuan->keterangan,
            'type' => optional($peneranganSatuan->files->first())->type ?? 'image'
        ];
    }

    public function includeFiles(PeneranganSatuan $peneranganSatuan){
        return $this->collection($peneranganSatuan->files, new PeneranganSatuanFileTransformer);
    }
}
