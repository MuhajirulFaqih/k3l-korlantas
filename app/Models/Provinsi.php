<?php

namespace App\Models;

use App\Models\Kabupaten;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{

    /**
     * Nama tabel yang dipakai oleh model.
     *
     * @var string
     */
    protected $table = 'wil_provinsi';

    /**
     * Nama kolom yang dipakai sebagai primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id_prov';

    /**
     * Relasi ke model Kabupaten dalam Provinsi bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'id_prov', 'id_prov');
    }
}
