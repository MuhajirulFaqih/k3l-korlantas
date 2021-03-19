<?php

namespace App\Models;

use App\Models\Kabupaten;
use App\Models\Kelurahan;
use Illuminate\Database\Eloquent\Model;

class JenisWilayah extends Model
{

    /**
     * Nama tabel yang dipakai oleh model.
     *
     * @var string
     */
    protected $table = 'wil_jenis';

    /**
     * Relasi ke model Kabupaten dengan jenis bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::app, 'id_jenis', 'id_jenis');
    }

    /**
     * Relasi ke model Kelurahan dengan jenis bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::app, 'id_jenis', 'id_jenis');
    }
}
