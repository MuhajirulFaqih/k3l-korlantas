<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Personil extends Model
{
    use HasFactory;

    protected $table = 'personil';

    protected $fillable = ['nrp', 'nama', 'id_pangkat', 'id_jabatan', 'id_kesatuan', 'kelamin', 'status_dinas', 'w_status_dinas', 'bearing', 'lat', 'lng', 'no_telp', 'alamat', 'ptt_ht', 'id_kec'];

    protected $casts = ['ptt_ht' => 'boolean'];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'id_pangkat');
    }

    public function kesatuan()
    {
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan');
    }

    public function auth()
    {
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function dinas()
    {
        return $this->belongsTo(Dinas::class, 'status_dinas');
    }

    public function logStatus(){
        return $this->hasMany(LogPersonil::class, 'id_personil', 'id');
    }

    public function ambilId(){
        $ids = User::where('jenis_pemilik', 'personil')->get()->pluck('id')->all();

        return collect($ids);
    }

    public function ambilIdLain($id){
        $tokens = User::where('jenis_pemilik', 'personil')->where('id_pemilik', '<>', $id)->get()->pluck('id')->all();
        return collect($tokens);
    }

    public function ambilToken()
    {
        $tokens = User::where('jenis_pemilik', 'personil')->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    public function ambilTokenLain($id)
    {
        $tokens = User::where('jenis_pemilik', 'personil')->where('id_pemilik', '<>', $id)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    public function ambilIdUserByKesatuan($kesatuan){
        $personil = DB::table('user as up')
            ->select('up.id')
            ->join('personil as p', function ($join){
                $join->on('p.id', '=', 'up.id_pemilik')
                    ->on('up.jenis_pemilik', '=', DB::raw('\'personil\''));
            })
            ->whereIn('p.id_kesatuan', $kesatuan);

        return $personil->get()->pluck('id');
    }

    public function ambilIdUserById($ids){
        $token = User::whereIn('id_pemilik', $ids)->where('jenis_pemilik', 'personil')->get()->pluck('id');

        return $token;
    }


    public function ambilTokenByKesatuan($kesatuan)
    {
        $personil = DB::table('user as up')
            ->select('up.id')
            ->join('personil as p', function ($join){
                $join->on('p.id', '=', 'up.id_pemilik')
                    ->on('up.jenis_pemilik', '=', DB::raw('\'personil\''));
            })
            ->whereIn('p.id_kesatuan', $kesatuan);

        $personil = $personil->get()->pluck('id')->all();

        $tokens = User::whereIn('id', $personil)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();

        return collect($tokens);
    }

    public function ambilTokenById($id_personil)
    {
        $personil = DB::table('user as up')
            ->select('up.id')
            ->join('personil as p', function ($join){
                $join->on('p.id', '=', 'up.id_pemilik')
                    ->on('up.jenis_pemilik', '=', DB::raw('\'personil\''));
            })
            ->whereIn('p.id', $id_personil);

        $personil = $personil->get()->pluck('id')->all();
        $tokens = User::whereIn('id', $personil)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    /**
     * Memproses query mentah untuk mendapatkan personil dengan
     * lokasi terdekat dari koordinat bersangkutan.
     *
     * @param float $lat
     * @param float $lng
     * @return \Illuminate\Database\Query\Expression
     */
    private function kolomHitungJarak($lat, $lng)
    {
        /*return DB::raw(
            '((ACOS(SIN('.$lat.' * PI() / 180) * SIN(lat * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * COS(('.$lng.' - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) AS jarak'
        );*/
        return DB::raw(
            '(
                6371 *
                acos(
                    cos( radians( '.$lat.' ) ) *
                    cos( radians( `lat` ) ) *
                    cos(
                        radians( `lng` ) - radians( '.$lng.' )
                    ) +
                    sin(radians('.$lat.')) *
                    sin(radians(`lat`))
                )
            ) AS jarak'
        );
    }

    public function terdekat(float $lat, float $lng){
        $personil = $this->select(
            'id', $this->kolomHitungJarak($lat, $lng)
        )->where('updated_at', '>', Carbon::now()->subHours(5))->whereNotNull('lat')->whereNotNull('lng')->where('status_dinas', '!=', '9')->orderBy('jarak')->get();

        $collection = collect();

        foreach ($personil as $row) {
            $single = $this->find($row->id);
            $collection->push([
                'id' => $row->id,
                'nama' => $single->nama,
                'lat' => $single->lat,
                'lng' => $single->lng,
                'lat, lng' => $single->lat. ",". $single->lng,
                'jarak' => $row->jarak,
                'updated_at' => $single->updated_at
            ]);
        }
        $collection = $collection->slice(0,50);
        $collection = $collection->sortBy('jarak');
        return $collection->slice(0, 5);
    }

    public function scopeTerlacak($query, $type = 'day', $value = 1, $jenis)
    {
        $query = $query->where(function($q) {
            $q->whereIn('id', function($sub) {
                $sub->select('u.id_pemilik')
                    ->from('user as u')
                    ->join('oauth_access_tokens as o', 'u.id', '=', 'o.user_id')
                    ->where('u.jenis_pemilik', 'personil')
                    ->where('o.revoked', '!=' , 1);
            });
        })
            ->where([['lat', '!=', 0.0], ['lng', '!=', 0.0]]);

        switch ($jenis) {
            case 'dinas':
                $query->where('status_dinas', '!=', '9');
                break;
            case 'lepas_dinas':
                $query->where('status_dinas', '=', '9');
                break;
            default:
                break;
        }

        switch ($type) {
            case 'day':
                $query->whereBetween('updated_at', [Carbon::now()->subDay($value)->toDateTimeString(), Carbon::now()->toDateTimeString()]);
                break;
            case 'hour':
                $query->whereBetween('updated_at', [Carbon::now()->subHour($value)->toDateTimeString(), Carbon::now()->toDateTimeString()]);
                break;
            case 'minute':
                $query->whereBetween('updated_at', [Carbon::now()->subMinute($value)->toDateTimeString(), Carbon::now()->toDateTimeString()]);
                break;
            case 'second':
                $query->whereBetween('updated_at', [Carbon::now()->subSecond($value)->toDateTimeString(), Carbon::now()->toDateTimeString()]);
                break;
            default:
                $query->whereBetween('updated_at', [Carbon::now()->subDay($value)->toDateTimeString(), Carbon::now()->toDateTimeString()]);
        }
        return $query;
    }

    public function scopeSearch($query, $filter)
    {
        if ($filter == null) {
            return $query;
        }

        return $query->whereIn(
            'id',
            DB::table('personil as p')
                ->select('p.id')
                ->leftJoin('pers_jabatan as j', 'p.id_jabatan', '=', 'j.id')
                ->leftJoin('pers_pangkat as pg', 'p.id_pangkat', '=', 'pg.id')
                ->leftJoin('dinas as d', 'p.status_dinas', '=', 'd.id')
                ->whereNull('p.deleted_at')
                ->whereRaw("CONCAT(
                    p.nrp, '||', p.nama, '||', j.jabatan, '||',
                    pg.pangkat, '||', d.kegiatan
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
        );
    }
}
