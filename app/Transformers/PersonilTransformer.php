<?php

/**
 * @Author: Alan
 * @Date:   2018-09-25 10:23:03
 * @Last Modified by:   Alan
 * @Last Modified time: 2018-09-25 16:40:16
 */
namespace App\Transformers;

use DB;
use App\Models\Dinas;
use App\Models\Personil;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;
use App\Models\Pengaturan;
use App\Traits\CheckStatusPersonil;

class PersonilTransformer extends TransformerAbstract
{
    use CheckStatusPersonil;
    /**
     * A Fractal transformer.
     *
     * @return array
     */

    protected $defaultIncludes = ['dinas'];

    protected $availableIncludes = ['bhabin'];

    public function transform(Personil $personil)
    {
        return [
            'id' => $personil->id,
            'nrp' => $personil->nrp,
            'nama' => $personil->nama,
            'alamat' => $personil->alamat,
            'no_telp' => $personil->no_telp,
            'id_pangkat' => $personil->id_pangkat,
            'pangkat' => $personil->pangkat->pangkat,
            'pangkat_lengkap' => $personil->pangkat->pangkat_lengkap,
            'id_jabatan' => $personil->id_jabatan,
            'jabatan' => $personil->jabatan->jabatan,
            'status_pimpinan' => $personil->jabatan->status_pimpinan,
            'id_kesatuan' => $personil->id_kesatuan,
            'kesatuan' => $personil->kesatuan->kesatuan,
            'banner_grid' => url('api/upload/' . ($personil->kesatuan->banner_grid ?? Pengaturan::getByKey('default_banner_grid')->first()->nilai)),
            'induk' => $personil->kesatuan->induk,
            'mulai_dinas' => $personil->w_status_dinas,
            'icon' => optional($personil->dinas)->icon ? url("api/upload/{$personil->dinas->icon}") : ($personil->kesatuan->icon ? url("api/upload/pin-personil/{$personil->kesatuan->icon}") : url("api/upload/pin-personil/personil-1.png")),
            'sispammako' => $personil->jabatan->sispammako,
            'foto' => Storage::exists("personil/".$personil->nrp.".jpg") ? url('api/upload/personil/' . $personil->nrp . '.jpg').'?time='.Storage::lastModified('personil/'.$personil->nrp.'.jpg') : url('api/upload/personil/pocil.jpg'),
            'terakhir_diupdate' => (string)$personil->updated_at,
            'ptt_ht' => $personil->ptt_ht,
            'lat' => $personil->lat,
            'lng' => $personil->lng,
            'kapolsek' => strpos($personil->jabatan->jabatan, 'KAPOLSEK') === 0 ? $personil->kesatuan->id_kec : null,
            'isBhabin' => $personil->bhabin !== null,
            'isDanaDesa' => $personil->jabatan->aksess_dana_desa,
            'list_kelurahan' => optional(optional($personil->bhabin)->kelurahan)->map(function ($val) {
                return $val->id_kel;
            }),
            'menu_tigapilar' => in_array($personil->id_jabatan, explode(',', env('TIGAPILAR', ''))) || $personil->bhabin != null,
            'bhabin_kel' => $personil->bhabin ? $personil->bhabin->kelurahan->implode('nama', ', ') : null,
            'loginStatus' => $this->CheckLoginPersonil($personil),
            'activeStatus' => $this->CheckActivePersonil($personil),
        ];
    }

    public function includeDinas(Personil $personil)
    {
        return $personil->dinas ? $this->item($personil->dinas, new DinasTransformer()) : null;
    }

    public function includeBhabin(Personil $personil)
    {
        return $personil->bhabin ? $this->item($personil->bhabin, new BhabinTransformer()) : null;
    }
}
