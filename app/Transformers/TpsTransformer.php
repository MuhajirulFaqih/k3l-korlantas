<?php

namespace App\Transformers;

use App\Models\Paslon;
use App\Models\Personil;
use App\Models\Tps;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class TpsTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['personil'];
    protected $availableIncludes = ['paslon'];

    /**
     * A Fractal transformer.
     *
     * @param Tps $itemTps
     * @return array
     */
    public function transform(Tps $itemTps)
    {
        return [
            'id' => $itemTps->id,
            'nama' => $itemTps->nama,
            'desa' => $itemTps->deskel ? strtoupper("{$itemTps->deskel->jenis->nama} {$itemTps->deskel->nama}") : null,
            'lat' => $itemTps->lat,
            'lng' => $itemTps->lng,
            'bap' => $itemTps->bap ? url('api/upload/'.$itemTps->bap) : null,
            'ket' => $itemTps->ket,
            'tidak_sah' => $itemTps->tidak_sah,
            'hadir' => $itemTps->tidak_sah + $itemTps->perolehan_suara->sum('suara')
        ];
    }

    public function includePersonil(Tps $itemTps){
        return $this->collection($itemTps->personil, function (Personil $itemPersonil){
            return [
                'id' => $itemPersonil->id,
                'nama' => $itemPersonil->pangkat->pangkat." ".$itemPersonil->nama,
                'nrp' => $itemPersonil->nrp,
                'no_hp' => $itemPersonil->no_telp,
                'jabatan' => $itemPersonil->jabatan->jabatan,
                'kesatuan' => $itemPersonil->kesatuan->kesatuan,
                'no_telp' => $itemPersonil->no_telp,
                'foto' => Storage::exists("personil/".$itemPersonil->nrp.".jpg") ? url('api/upload/personil/' . $itemPersonil->nrp . '.jpg').'?time='.Storage::lastModified('personil/'.$itemPersonil->nrp.'.jpg') : url('api/upload/personil/pocil.jpg'),
            ];
        });
    }

    public function includePaslon(Tps $itemTps){
        return $this->collection($itemTps->paslon, new PaslonTransformer($itemTps));
    }
}
