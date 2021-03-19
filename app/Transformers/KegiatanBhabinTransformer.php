<?php

namespace App\Transformers;

use App\Models\BidangKegiatanBhabin;
use App\Models\IndikatorKegiatanBhabin;
use App\Models\JenisKegiatanBhabin;
use App\Models\KategoriKegiatanBhabin;
use App\Models\KegiatanBhabin;
use App\Models\MasyarakatKegiatanBhabin;
use App\Models\Transformers\KecamatanTransformer;
use App\Models\Transformers\MasyarakatKegiatanBhabinTransformer;
use League\Fractal\TransformerAbstract;

class KegiatanBhabinTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];

    protected $availableIncludes = ['tipe', 'jenis', 'kategori', 'bidang', 'masyarakat', 'kecamatan'];
    protected $prev = false;

    public function __construct($prev = false)
    {
        $this->prev = $prev;
    }
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(KegiatanBhabin $kegiatan)
    {
        return [
            'id' => $kegiatan->id,
            'lokasi' => $kegiatan->lokasi,
            'uraian' => $this->prev ? str_limit($kegiatan->uraian, 40) : $kegiatan->uraian,
            'ringkasan' => $kegiatan->ringkasan,
            'para_pihak' => $kegiatan->para_pihak,
            'kronologi' => $kegiatan->kronologi,
            'solusi' => $kegiatan->solusi,
            'kegiatan' => $kegiatan->kegiatan,
            'pelaksanaan' => $kegiatan->pelaksanaan,
            'sumber_info' => $kegiatan->sumber_info,
            'nilai_informasi' => $kegiatan->nilai_informasi,
            'waktu_kegiatan' => $kegiatan->waktu_kegiatan->toDateTimeString(),
            'lat' => $kegiatan->lat,
            'lng' => $kegiatan->lng,
            'sasaran' => $kegiatan->sasaran,
            'kuat_pers' => $kegiatan->kuat_pers,
            'hasil' => $kegiatan->hasil,
            'jml_giat' => $kegiatan->jml_giat,
            'jml_tsk' => $kegiatan->jml_tsk,
            'bb' => $kegiatan->jml_tsk,
            'perkembangan' => $kegiatan->perkembangan,
            'dasar' => $kegiatan->dasar,
            'keterangan' => $this->prev ? str_limit($kegiatan->uraian, 40) : $kegiatan->uraian,
            'dokumentasi' => $kegiatan->dokumentasi ? url('api/upload/' . $kegiatan->dokumentasi) : null,
        ];
    }

     public function includeUser(KegiatanBhabin $kegiatan)
    {
        return $this->item($kegiatan->user, new UserTransformer());
    }

    public function includeTipe(KegiatanBhabin $kegiatan)
    {
        return $this->item($kegiatan->tipe, function (IndikatorKegiatanBhabin $tipeLaporan) {
            return $tipeLaporan->toArray();
        });
    }

    public function includeJenis(KegiatanBhabin $kegiatan)
    {
        if ($kegiatan->jenis)
            return $this->item($kegiatan->jenis, function (JenisKegiatanBhabin $jenisKegiatan) {
            return $jenisKegiatan->toArray();
        });
    }

    public function includeKategori(KegiatanBhabin $kegiatan)
    {
        if ($kegiatan->kategori)
            return $this->item($kegiatan->kategori, function (KategoriKegiatanBhabin $kategori) {
            return $kategori->toArray();
        });
    }

    public function includeBidang(KegiatanBhabin $kegiatan)
    {
        if ($kegiatan->id_bidang)
            return $this->item($kegiatan->bidang, function (BidangKegiatanBhabin $bidang) {
            return $bidang->toArray();
        });
    }

    public function includeMasyarakat(KegiatanBhabin $kegiatan)
    {
        if ($kegiatan->masyarakat)
            return $this->collection($kegiatan->masyarakat, new MasyarakatKegiatanBhabinTransformer());
    }

    public function includeKecamatan(KegiatanBhabin $kegiatan)
    {
        if ($kegiatan->kecamatan)
            return $this->item($kegiatan->kecamatan, new KecamatanTransformer());
    }
}
