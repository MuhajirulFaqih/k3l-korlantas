<?php

namespace App\Transformers;

use App\Models\Surat;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SuratTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['jabatan', 'disposisi'];

    protected $klasifikasi = [
        'biasa' => 'Biasa',
        'rahasia' => 'Rahasia',
        'telegram_rahasia' => 'SURAT TELEGRAM RAHASIA',
        'telegram' => 'SURAT TELEGRAM',
        'undangan' => 'UNDANGAN',
        'nota_dinas' => 'NOTA DINAS',
        'sprin' => 'NOTA DINAS',
        'kep' => 'KEP',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Surat $itemSurat)
    {
        return [
            'id' => $itemSurat->id,
            'no_agenda' => $itemSurat->no_agenda,
            'klasifikasi' => $this->klasifikasi[$itemSurat->klasifikasi],
            'derajat' => $itemSurat->derajat,
            'tanggal' => $itemSurat->tanggal,
            'id_asal' => $itemSurat->id_asal,
            'asal' => $itemSurat->jenis->jenis,
            'waktu_diterima' => $itemSurat->waktu_diterima,
            'waktu_diterima_format' => Carbon::createFromFormat('Y-m-d H:i:s', $itemSurat->waktu_diterima)->format('d-m-Y H:i:s'),
            'nomor' => $itemSurat->nomor,
            'pengirim' => $itemSurat->pengirim,
            'perihal' => $itemSurat->perihal,
            'file' => $itemSurat->file ? url('api/upload/' . $itemSurat->file) : null,
            'created_at' => $itemSurat->created_at->toDateTimeString()
        ];
    }

    public function includeDisposisi(Surat $itemSurat)
    {
        return $this->collection($itemSurat->disposisi, new SuratDisposisiTransformer());
    }

    public function includeJabatan(Surat $itemSurat)
    {
        return $this->collection($itemSurat->jabatan, new JabatanTransformer());
    }
}
