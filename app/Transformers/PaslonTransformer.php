<?php

namespace App\Transformers;

use App\Models\Paslon;
use League\Fractal\TransformerAbstract;

class PaslonTransformer extends TransformerAbstract
{
    private $tps;

    public function __construct($tps = null)
    {
    	$this->tps = $tps;
    }
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Paslon $itemPaslon)
    {
        $suara = $this->tps ? $this->tps->perolehan_suara->where('id_paslon', $itemPaslon->id)->first() : null;

        return [
            'id' => $itemPaslon->id,
            'nama_ka' => $itemPaslon->nama_ka,
            'nama_waka' => $itemPaslon->nama_waka,
            'foto' => $itemPaslon->foto ? url('api/upload/'.$itemPaslon->foto) : url('api/upload/paslon/paslon.png'),
            'no_urut' => $itemPaslon->no_urut,
            'jenis' => $itemPaslon->jenis,
            'suara' => $suara ? $suara->suara : 0,
            'id_tps' => $this->tps ? $this->tps->id : null,
            'id_perolehan' => $this->tps ? ($itemPaslon->perolehan_suara->where('id_tps', $this->tps->id)->first() ? $itemPaslon->perolehan_suara->where('id_tps', $this->tps->id)->first()->id : null ) : null,
            'total_suara' => $itemPaslon->total_suara,
        ];
    }
}
