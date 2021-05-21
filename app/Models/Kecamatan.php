<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;
    /**
     * Nama tabel yang dipakai oleh model.
     *
     * @var string
     */
    protected $table = 'wil_kecamatan';

    /**
     * Nama kolom yang dipakai sebagai primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id_kec';

    /**
     * Relasi ke model Kabupaten.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kab', 'id_kab');
    }

    /**
     * Relasi ke model Kelurahan dalam Kecamatan bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'id_kec', 'id_kec');
    }
}
