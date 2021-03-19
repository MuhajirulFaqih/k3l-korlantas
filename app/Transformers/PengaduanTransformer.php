<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Pengaduan;

class PengaduanTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['komentar'];
    protected $defaultIncludes = ['user'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Pengaduan  $itemPengaduan)
    {
        return [
            'id' => $itemPengaduan->id,
            'lat' => $itemPengaduan->lat,
            'lng' => $itemPengaduan->lng,
            'lokasi' => $itemPengaduan->lokasi,
            'jumlah_komentar' => $itemPengaduan->komentar->count(),
            'keterangan' => $itemPengaduan->keterangan,
            'created_at' => $itemPengaduan->created_at->toDateTimeString(),
            'foto' => $itemPengaduan->foto ? url('api/upload/' . $itemPengaduan->foto) : null,
        ];
    }

    public function includeKomentar(Pengaduan $itemPengaduan){
        return $this->collection($itemPengaduan->komentar, new KomentarTransformer);
    }

    public function includeUser(Pengaduan $itemPengaduan){
        return $this->item($itemPengaduan->user, new UserTransformer);
    }
}
