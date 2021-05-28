<?php


namespace App\Transformers;

use App\Models\Pengaturan;
use App\Models\Personil;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;
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

    public function transform($personil)
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
            'kesatuan_lengkap' => in_array($personil->kesatuan->level, [1, 2]) ? $personil->kesatuan->kesatuan :  $personil->kesatuan->kesatuan .' '.$personil->kesatuan->parent->kesatuan,
            'banner_grid' => $personil->kesatuan->banner_grid != null ? url('api/upload/' . ($personil->kesatuan->banner_grid ?? Pengaturan::getByKey('default_banner_grid')->first()->nilai )) : null,
            'induk' => $personil->kesatuan->induk,
            'mulai_dinas' => $personil->w_status_dinas,
            'icon' => optional($personil->dinas)->icon ? url("api/upload/{$personil->dinas->icon}") : ($personil->kesatuan->icon ? url("api/upload/pin-personil/{$personil->kesatuan->icon}") : url("api/upload/pin-personil/personil-1.png")),
            'sispammako' => $personil->jabatan->sispammako,
            'foto' => Storage::exists("personil/".$personil->nrp.".jpg") ? url('api/upload/personil/' . $personil->nrp . '.jpg').'?time='.Storage::lastModified('personil/'.$personil->nrp.'.jpg') : url('api/upload/personil/presisi.jpg'),
            'terakhir_diupdate' => (string)$personil->updated_at,
            'ptt_ht' => $personil->ptt_ht,
            'lat' => $personil->lat,
            'lng' => $personil->lng,
            'pers_polres' => in_array(optional($personil->kesatuan->jenis)->jenis, ['POLRES', 'SATKER_POLRES', 'POLSEK']),
            'pers_polda' => in_array(optional($personil->kesatuan->jenis)->jenis, ['POLDA', 'SATKER POLDA', 'SUBSATKER POLDA']),
            'kapolsek' => strpos($personil->jabatan->jabatan, 'KAPOLSEK') === 0 ? $personil->kesatuan->id_kec : null,
            'isDanaDesa' => $personil->jabatan->aksess_dana_desa,
            // 'menu_tigapilar' => in_array($personil->id_jabatan, explode(',', env('TIGAPILAR', ''))) || $personil->bhabin != null,
            'loginStatus' => $this->CheckLoginPersonil($personil),
            'activeStatus' => $this->CheckActivePersonil($personil),
        ];
    }

    public function includeDinas($personil)
    {
        return $personil->dinas ? $this->item($personil->dinas, new DinasTransformer()) : null;
    }
}
