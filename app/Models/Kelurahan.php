<?php

namespace App\Models;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kelurahan extends Model
{
    use HasFactory, UserTimezoneAware;
    /**
     * Nama tabel yang dipakai oleh model.
     *
     * @var string
     */
    protected $table = 'wil_kelurahan';

    /**
     * Relasi model yang langsung terhubung.
     *
     * @var array
     */
    protected $with = ['jenis'];

    /**
     * Nama kolom yang dipakai sebagai primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id_kel';

    /**
     * Relasi ke model Kecamatan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec', 'id_kec');
    }

    /**
     * Relasi ke model Jenis.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jenis()
    {
        return $this->belongsTo(JenisWilayah::class, 'id_jenis', 'id_jenis');
    }

    public function bhabin()
    {
        return $this->belongsToMany(Bhabin::class, 'bhabin_kelurahan', 'id_kel', 'id_bhabin');
    }

    public function profil()
    {
        return $this->hasOne(ProfilKelurahan::class, 'id_kel');
    }

    public function scopeFilteredBhabin($query, $filter, $id_kab, $id_kec){
        $join = DB::table('wil_kelurahan')
            ->select('id_kel')
            ->join('wil_kecamatan', 'wil_kecamatan.id_kec', '=', 'wil_kelurahan.id_kec')
            ->join('wil_kabupaten', 'wil_kabupaten.id_kab', '=', 'wil_kecamatan.id_kab')
            ->whereRaw("CONCAT(wil_kelurahan.nama, '||', wil_kecamatan.nama, '||', wil_kabupaten.nama) LIKE ?", ['%'.addslashes($filter).'%']);

        if ($id_kab)
            $join->where('wil_kabupaten.id_kab', $id_kab);

        if ($id_kec)
            $join->where('wil_kecamatan.id_kec', $id_kec);

        $query->whereIn('id_kel', $join);
        return $query;
    }

    public function scopeSemua($query, $id_kab)
    {
        return $query->whereIn(
            'id_kec',
            DB::table('wil_kecamatan')
                ->select('id_kec')
                ->whereIn('id_kab', $id_kab)
                ->get()->pluck('id_kec')->all()
        );
    }

    public function scopeGetByProv($query, $wilayah) {

        $where = env('TINGKAT_DANA_DESA', 'provinsi') == 'kabupaten' ? 'wkb.id_kab' : 'wkb.id_prov';

        return $query->whereIn(
            'id_kel',
            DB::table('wil_kelurahan as k')
                ->select('k.id_kel')
                ->join('wil_kecamatan as wk', 'k.id_kec', '=', 'wk.id_kec')
                ->join('wil_kabupaten as wkb', 'wk.id_kab', '=', 'wkb.id_kab')
                ->where($where, $wilayah)
                ->get()->pluck('id_kel')->all()
        );
    }

    public function scopeFilteredDanaDesa($query, $filter, $wilayah)
    {
        if ($filter == null) {
            return $query;
        }

        $where = env('TINGKAT_DANA_DESA', 'provinsi') == 'kabupaten' ? 'wkb.id_kab' : 'wkb.id_prov';

        return $query->whereIn(
            'id_kel',
            DB::table('wil_kelurahan as k')
                ->select('k.id_kel')
                ->join('wil_kecamatan as wk', 'k.id_kec', '=', 'wk.id_kec')
                ->join('wil_kabupaten as wkb', 'wk.id_kab', '=', 'wkb.id_kab')
                ->where($where, $wilayah)
                ->whereRaw("CONCAT(
                    k.nama, '||', wk.nama, '||', wkb.nama
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id_kel')->all()
        );
    }

}
