<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisWilayah extends Model
{
    use HasFactory, UserTimezoneAware;

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
        return $this->hasMany(Kabupaten::class, 'id_jenis', 'id_jenis');
    }

    /**
     * Relasi ke model Kelurahan dengan jenis bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'id_jenis', 'id_jenis');
    }
}
