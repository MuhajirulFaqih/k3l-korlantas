<?php

namespace App\Transformers;

use App\Models\Kejadian;
use Illuminate\Support\Str;
use App\Traits\AudioKejadian;
use League\Fractal\TransformerAbstract;

class KejadianTransformer extends TransformerAbstract
{
    use AudioKejadian;
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    protected $defaultIncludes = ['komentar','user', 'tindak_lanjut', 'nearby'];

    protected $availableIncludes = ['tindak_lanjut'];

    protected $with = ['user','jenis'];

    private $prev = false;

    public function __construct($prev = false)
    {
        $this->prev = $prev;
    }

    public function transform(Kejadian $kejadian)
    {
        $tindakLanjutStatus = [
            'tkp' => 'Menuju ke TKP',
            'proses_penanganan' => 'Proses Penanganan',
            'selesai' => 'Selesai'
        ];
        return [
            'id'             => $kejadian->id,
            'w_kejadian'     => $kejadian->w_kejadian->toDateTimeString(),
            'kejadian'       => $kejadian->kejadian,
            'lokasi'         => $kejadian->lokasi,
            'keterangan'     => $this->prev ? Str::limit($kejadian->keterangan, 70, '...') : $kejadian->keterangan,
            'lat'            => $kejadian->lat,
            'lng'            => $kejadian->lng,
            'gambar'         => $kejadian->gambar ? url('api/upload/'.$kejadian->gambar) : null,
            'status'         => $kejadian->tindak_lanjut->count() ? $tindakLanjutStatus[$kejadian->tindak_lanjut->sortByDesc('created_at')->first()->status] : ($kejadian->verifikasi == '1' ? 'Laporan Baru' : 'Belum Diverifikasi'),
            'verifikasi'     => $kejadian->verifikasi,
            'id_darurat'     => $kejadian->id_darurat,
            'follow_me'      => $kejadian->follow_me,
            'selesai'        => $kejadian->selesai,
            'audio'        => $this->audioKejadian($kejadian->kejadian),
        ];
    }

    public function includeUser(Kejadian $kejadian)
    {
        return $this->item($kejadian->user, new UserTransformer);
    }

    public function includeTindakLanjut(Kejadian $kejadian){
        return $this->collection($kejadian->tindak_lanjut, new TindakLanjutTransformer());
    }

    public function includeKomentar(Kejadian $kejadian)
    {
        return $this->collection($kejadian->komentar()->paginate(5), new KomentarTransformer);
    }

    public function includeNearby(Kejadian $itemKejadian){
        return $this->collection($itemKejadian->nearby, new PersonilTerdekatTransformer());
    }
}
