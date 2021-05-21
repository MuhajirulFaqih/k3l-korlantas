<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang dipakai oleh model.
     *
     * @var string
     */
    protected $table = 'wil_kabupaten';

    /**
     * Nama kolom yang dipakai sebagai primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id_kab';

    /**
     * Relasi ke model Provinsi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov', 'id_prov');
    }

    /**
     * Relasi ke model Kecamatan dalam Kabupaten bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'id_kab', 'id_kab');
    }
}
