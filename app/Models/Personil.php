<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
// use Laravel\Scout\Searchable;

class Personil extends Model
{
    use SoftDeletes;

    /*public $asYouType = true;
    public $fuzziness = true;

    protected $inMemory = true;*/

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
        return $this->belongsTo(Kesatuan::class, 'id_kesatuan')->withTrashed();
    }

    public function auth()
    {
        return $this->morphOne(User::class, 'auth', 'jenis_pemilik', 'id_pemilik');
    }

    public function dinas()
    {
        return $this->belongsTo(Dinas::class, 'status_dinas');
    }

    public function bhabin()
    {
        return $this->hasOne(Bhabin::class, 'id_personil', 'id');
    }

    public function pamtps(){
        return $this->belongsToMany(Tps::class, 'tps_personil', 'id_personil', 'id_tps');
    }

    public function logStatus(){
        return $this->hasMany(LogPersonil::class, 'id_personil', 'id');
    }

    public function ambilId(){
        $ids = User::where('jenis_pemilik', 'personil')->get()->pluck('id')->all();

        return collect($ids);
    }

    public function ambilIdLain($id){
        $tokens = User::where('jenis_pemilik', 'personil')->where('id_pemilik', '<>', $id)->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    public function ambilToken()
    {
        /*$personil = $this->all();
        $tokens = [];
        foreach ($personil as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }*/
        $tokens = User::where('jenis_pemilik', 'personil')->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    public function ambilTokenLain($id)
    {
        /*$personil = $this->where('id', '<>', $id)->get();
        $tokens = [];
        foreach ($personil as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }*/
        $tokens = User::where('jenis_pemilik', 'personil')->where('id_pemilik', '<>', $id)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    public function ambilTokenByKesatuan($kesatuan)
    {
        // $personil = $this->whereIn('id_kesatuan', $kesatuan)->get();
        // $tokens = [];
        // foreach ($personil as $row) {
        //     if ($row->auth !== null && !empty($row->auth->fcm_id)) {
        //         $tokens[] = $row->auth->fcm_id;
        //     }
        // }
        // return collect($tokens);

        $personil = DB::table('user as up')
            ->select('up.id')
            ->join('personil as p', function ($join){
                $join->on('p.id', '=', 'up.id_pemilik')
                    ->on('up.jenis_pemilik', '=', DB::raw('\'personil\''));
            })
            ->whereIn('p.id_kesatuan', $kesatuan);

        $personilBhabin = DB::table('user as ub')
                ->select('ub.id')
                ->join('bhabin as b', function ($join){
                    $join->on('b.id', '=', 'ub.id_pemilik')
                        ->on('ub.jenis_pemilik', '=', DB::raw('\'bhabin\''));
                })
                ->join('personil as p', 'p.id', '=', 'b.id_personil')
                ->whereIn('p.id_kesatuan', $kesatuan)
                ->union($personil)
                ->get()->pluck('id')->all();

        $tokens = User::whereIn('id', $personilBhabin)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();

        return collect($tokens);
    }

    public function ambilTokenById($id_personil)
    {
        /*$personil = $this->whereIn('id', $id_personil)->get();
        $tokens = [];
        foreach ($personil as $row) {
            if ($row->auth !== null && !empty($row->auth->fcm_id)) {
                $tokens[] = $row->auth->fcm_id;
            }
        }*/

        $personil = DB::table('user as up')
            ->select('up.id')
            ->join('personil as p', function ($join){
                $join->on('p.id', '=', 'up.id_pemilik')
                    ->on('up.jenis_pemilik', '=', DB::raw('\'personil\''));
            })
            ->whereIn('p.id', $id_personil);

        $personilBhabin = DB::table('user as ub')
                ->select('ub.id')
                ->join('bhabin as b', function ($join){
                    $join->on('b.id', '=', 'ub.id_pemilik')
                        ->on('ub.jenis_pemilik', '=', DB::raw('\'bhabin\''));
                })
                ->whereIn('b.id_personil', $id_personil)
                ->union($personil)
                ->get()->pluck('id')->all();
        $tokens = User::whereIn('id', $personilBhabin)->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        return collect($tokens);
    }

    /**
     * Memproses query mentah untuk mendapatkan Bhabin dengan
     * lokasi terdekat dari koordinat bersangkutan.
     *
     * @param  float                                $lat
     * @param  float                                $lng
     * @return \Illuminate\Database\Query\Builder
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

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['latitude'] = $array['lat'] ?? null;
        $array['longitude'] = $array['lng'] ?? null;
        $array['jabatan'] = $this->jabatan['jabatan'];
        $array['status_dinas'] = $this->dinas['kegiatan'];
        $array['bhabin_kel'] = optional(optional($this->bhabin)->kelurahan)->implode('nama', ', ');
        unset($array['auth']);
        unset($array['bhabin']);

        return $array;
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
                    })
                    ->orWhereIn('id', function($sub) {
                        $sub->select('b.id_personil')
                            ->from('bhabin as b')
                            ->join('user as u', 'u.id_pemilik', '=', 'b.id')
                            ->join('oauth_access_tokens as o', 'u.id', '=', 'o.user_id')
                            ->where('u.jenis_pemilik', 'bhabin')
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
                ->leftJoin('pers_kesatuan as k', 'p.id_kesatuan', '=', 'k.id')
                ->leftJoin('dinas as d', 'p.status_dinas', '=', 'd.id')
                ->whereNull('p.deleted_at')
                ->whereRaw("CONCAT(
                    p.nrp, '||', p.nama, '||', j.jabatan, '||', 
                    pg.pangkat, '||', k.kesatuan, '||', d.kegiatan
                ) LIKE ?", ['%' . addcslashes($filter, '%_') . '%'])
                ->get()->pluck('id')->all()
            );
    }

    public function dataPersonilPengirimKegiatanTerbanyak ($tanggal) {
        list($mulai, $selesai) = $tanggal;
        $mulai = date('Y-m-d', strtotime($mulai));
        $selesai = date('Y-m-d', strtotime($selesai));

        $data = DB::table('kegiatan as k')
                    ->selectRaw('p.nrp, p.nama, pp.pangkat, pj.jabatan, COUNT(k.id) AS jumlah')
                    ->join('user as u', 'k.id_user', '=', 'u.id')
                    ->join('personil as p', 'u.id_pemilik', '=', 'p.id')
                    ->join('pers_pangkat as pp', 'p.id_pangkat', '=', 'pp.id')
                    ->join('pers_jabatan as pj', 'p.id_jabatan', '=', 'pj.id')
                    ->where('u.jenis_pemilik', '=', 'personil')
                    ->whereBetween(DB::raw('DATE(k.waktu_kegiatan)'), [ $mulai, $selesai ])
                    ->groupBy('p.id')
                    ->orderBy('jumlah', 'desc')
                    ->limit(10)
                    ->get();
        
        return $data;

    }
}