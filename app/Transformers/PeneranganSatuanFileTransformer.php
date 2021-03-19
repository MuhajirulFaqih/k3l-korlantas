<?php

namespace App\Transformers;

use App\Models\PeneranganSatuanFile;
use League\Fractal\TransformerAbstract;

class PeneranganSatuanFileTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PeneranganSatuanFile $peneranganSatuanFile)
    {
        return [
            'id' => $peneranganSatuanFile->id,
            'file' => $peneranganSatuanFile->type != 'video' ? url('api/upload/'.$peneranganSatuanFile->file) : $peneranganSatuanFile->file,
            'thumbnails' => $peneranganSatuanFile->thumbnails,
            'type' => $peneranganSatuanFile->type
        ];
    }
}
