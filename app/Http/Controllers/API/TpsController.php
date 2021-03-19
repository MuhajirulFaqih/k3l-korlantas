<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PerolehanSuara;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\Tps;
use App\Transformers\TpsTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\TpsEvent;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TpsController extends Controller
{
    public function getAll(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['bhabin', 'personil']))
            return response()->json(['error' => 'Terlarang'], 403);


        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;

        if ($personil->pamtps->count() > 0){
            $paginator = $personil->pamtps()->paginate(10);
            $collection = $paginator->getCollection();
        } else {
            $paginator = Tps::filtered($request->filter, $request->id_kec, $request->id_kel)->paginate(10);
            $collection = $paginator->getCollection();
        }

        return fractal()
            ->collection($collection)
            ->parseIncludes(['paslon'])
            ->transformWith(TpsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function get(Request $request, Tps $tps){

        $user = $request->user();
        if($user->jenis_pemilik != 'admin')
            if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']) && !in_array($user->jenis_pemilik == 'personil' ? $user->pemilik->id : $user->pemilik->personil->id, $tps->personil->pluck('id_personil')))
                return response()->json(['error' => 'Anda tidak memiliki akses'], 403);

        return fractal()
            ->item($tps)
            ->transformWith(TpsTransformer::class)
            ->parseIncludes(['paslon'])
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function updateLokasi(Request $request, Tps $tps){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);


        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;


        if(!in_array($tps->id, $personil->pamtps->pluck('id')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'lat' => 'required',
            'lng' => 'required'
        ]);

        $update = $tps->update($validatedData);

        if (!$update)
            return response()->json(['error' => 'Terjadi kesalahan']);

        $data = fractal()
            ->item($tps)
            ->parseIncludes(['paslon'])
            ->transformWith(TpsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        event(new TpsEvent($data));
        

        return response()->json(['success' => true]);
    }

    public function perolehan(Request $request, Tps $tps){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);


        $personil = $user->jenis_pemilik == 'personil' ? $user->pemilik : $user->pemilik->personil;


        if(!in_array($tps->id, $personil->pamtps->pluck('id')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'lat' => 'required',
            'lng' => 'required',
            'bap' => 'nullable|image',
            'ket' => 'nullable',
            //'tidak_sah' => 'required',
            'paslon.*.id' => 'required',
            'paslon.*.suara' => 'required|numeric|min:1'
        ]);

        $updateTps = [
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'ket' => $validatedData['ket']
            //'tidak_sah' => $validatedData['tidak_sah']
        ];

        if ($request->file('bap')){
            $fileBap = $request->file('bap')->storeAs(
                'bap-tps',
                str_random(40).'.'.$request->bap->extension()
            );
            $updateTps['bap'] = $fileBap;
        }

        $update = $tps->update($updateTps);

        if (!$update)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        if (isset($validatedData['paslon'])){
            foreach($validatedData['paslon'] as $paslon){
                $tps->perolehan_suara()->updateOrInsert(
                    ['id_paslon' => $paslon['id'], 'id_tps' => $tps->id],
                    ['id_paslon' => $paslon['id'], 'suara' => $paslon['suara']]
                );
            }
        }

        $tps = Tps::find($tps->id);

        // Todo send ke monit


        return response()->json(['success' => true]);
    }

    public function tpsMonit(Request $request){
        $user = $request->user();


        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);


        $paginator = Tps::whereNotNull('lat')
                    ->whereNotNull('lng')
                    ->paginate(50);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes(['paslon'])
            ->transformWith(TpsTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function jumlahTps(Request $request, Tps $tps)
    {
        $tpsCount = $tps->whereNotNull('lat')->whereNotNull('lng')->count();        
        $persPamCount = $tps->getPam();     
        return response()->json(['tps' => $tpsCount, 'pers' => $persPamCount]);   
    }
}
