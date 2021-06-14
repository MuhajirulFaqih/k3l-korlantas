<?php
namespace App\Services;

use App\Models\Jabatan;
use App\Models\Kesatuan;
use App\Models\Pangkat;
use App\Models\Personil;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PersonilService
{
    public function fetchPersonil($nrp){
        $token = Cache::remember('sipp_token', 1440, function () {
            $response = Http::post(env('SIPP_BASE_API').'v1/login', [
                'username' => env('SIPP_USERNAME'),
                'password' => env('SIPP_PASSWORD')
            ]);

            if ($response->ok()){
                return $response->json('access_token');
            } else {
                return null;
            }
        });

        $requestData = Http::withToken($token)->get(env('SIPP_BASE_API').'v1/personel/singkat?nrp=' . $nrp);

        if ($requestData->ok()){
            $responseJson = $requestData->json();
            if (isset($responseJson['status']) && $responseJson['status'] == 200){
                $data = $responseJson['data'];

                try {
                    $satuan1 = $data['satuan1'] ? explode('-', $data['satuan1']) : null;
                    $satuan2 = $data['satuan2'] ? explode('-', $data['satuan2']) : null;
                    $satuan3 = $data['satuan3'] ? explode('-', $data['satuan3']) : null;
                    $satuan4 = $data['satuan4'] ? explode('-', $data['satuan4']) : null;

                    $kode1 = array_pop($satuan1);
                    $satuan1 = implode('-', $satuan1);

                    if ($satuan1 != env('APP_KESATUAN'))
                        return null;

                    $kesatuan1 = Kesatuan::where('kesatuan', $satuan1)->where('kesatuan', $satuan1)->where('level', 1)->first();

                    if (!$kesatuan1)
                        $kesatuan1 = Kesatuan::where('kesatuan', $satuan1)->where('level', 1)->first();

                    if ($satuan2){
                        $kode2 = array_pop($satuan2);
                        $satuan2 = implode('-', $satuan2);

                        $kesatuan2 = $kesatuan1->children()->where('kesatuan', $satuan2)->where('level', 2)->first();

                        if (!$kesatuan2)
                            $kesatuan2 = $kesatuan1->children()->create(['kesatuan' => $satuan2, 'level' => 2, 'kode_satuan' => $kode2]);
                    }

                    if ($satuan3){
                        $kode3 = array_pop($satuan3);
                        $satuan3 = implode('-', $satuan3);

                        $kesatuan3 = $kesatuan2->children()->where('kesatuan', $satuan3)->where('level', 3)->first();

                        if (!$kesatuan3)
                            $kesatuan3 = $kesatuan2->children()->create(['kesatuan' => $satuan3, 'level' => 3, 'kode_satuan' => $kode3]);
                    }

                    if ($satuan4){
                        $kode4 = array_pop($satuan4);
                        $satuan4 = implode('-', $satuan4);

                        $kesatuan4 = $kesatuan3->children()->where('kesatuan', $satuan4)->where('level', 4)->first();

                        if (!$kesatuan4)
                            $kesatuan4 = $kesatuan3->children()->create(['kesatuan' => $satuan4, 'level' => 3, 'kode_satuan' => $kode4]);
                    }

                    $kesatuan = null;

                    if ($satuan4 && $kesatuan4){
                        $kesatuan = $kesatuan4;
                    } else if($satuan3 && $kesatuan3) {
                        $kesatuan = $kesatuan3;
                    } else if($satuan2 && $kesatuan2){
                        $kesatuan = $kesatuan2;
                    } else {
                        $kesatuan = $kesatuan1;
                    }

                    $jabatan = Jabatan::firstOrCreate(['jabatan' => $data['jabatan'] ?? 'Tidak ada jabatan', 'id_kesatuan' => $kesatuan->id, 'status_pimpinan' => false]);
                    $pangkat = Pangkat::firstOrCreate(['pangkat' => $data['pangkat']]);

                    return [
                        'nrp' => $data['nrp'],
                        'id_kesatuan' => $kesatuan->id,
                        'id_jabatan' => $jabatan->id,
                        'id_pangkat' => $pangkat->id,
                        'nama' => $data['nama'],
                        'jabatan' => $data['jabatan'],
                        'kesatuan_lengkap' => in_array($kesatuan->level, [1,2]) ? $kesatuan->kesatuan : $kesatuan->parent->kesatuan .' '.$kesatuan->kesatuan,
                        'pangkat' => $data['pangkat'],
                        'handphone' => $data['handphone'],
                        'jenis_kelamin' => $data['jenis_kelamin'] == 'PRIA' ? 'L' : 'P',
                        'foto_file' => $data['foto_file']
                    ];
                } catch (\Exception $e){
                    Log::error("PersonilService",  $e->getTrace());
                    return null;
                }
            }
        }

        return null;
    }

    function getDistance($lat1, $lng1, $lat2, $lng2, $distance_unit = 'km'){
        $earthRadius = ($distance_unit == 'km' ? 6371 : 6371000);

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    function getDistances($locations, $distance_unit = 'km', $round = 2){
        $distance = 0;
        foreach ($locations as $index => $entry){
            if ($index < $locations->count() - 1){
                $next = $locations[$index + 1];

                $distance += $this->getDistance($entry->lat, $entry->lng, $next->lat, $next->lng, $distance_unit);
            }
        }

        return round($distance, $round);
    }

}
