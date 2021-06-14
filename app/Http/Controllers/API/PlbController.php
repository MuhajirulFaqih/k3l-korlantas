<?php

namespace App\Http\Controllers\API;

use App\Models\Plb;
use App\Models\Kesatuan;
use App\Models\Personil;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\PlbTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PlbController extends Controller
{

    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil']))
            return response()->json(['error' => 'Terlarang'], 403);
        
        list($orderBy, $direction) = $request->sort != '' ? explode(':', $request->sort) : explode(':', 'created_at:desc');

        $plb = Plb::with(['user'])->filtered($request->filter)->orderBy($orderBy, $direction);

        $paginator = $plb->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(PlbTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function store(Request $request) {
        $user = $request->user();

        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['personil'])){
            return response()->json(['error' => 'Terlarang'], 403);
        }

        $validatedData = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'id_kesatuan' => 'required|numeric',
            'keterangan' => 'required'
        ]);

        $user = $request->user();

        $data = [
            'id_user' => $user->id,
            'id_kesatuan' => $user->pemilik->id_kesatuan ?? null,
            'id_kesatuan_tujuan' => $request->id_kesatuan,
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'keterangan' => $validatedData['keterangan']
        ];

        $plb = Plb::create($data);
        
        //Start broadcast
        $broadcast = [
            'id' => $plb->id,
            'pesan' => $plb->keterangan,
            'nama' => $user->nama
        ];

        $personil = new Personil;
        
        if($plb->id_kesatuan_tujuan == 1) {
            $penerimaOneSignal = $personil->ambilIdLain($user->id);
            $penerima = $personil->ambilToken();
        } else {
            $kesatuanTujuan = Kesatuan::descendantsAndSelf($plb->id_kesatuan_tujuan)->pluck('id')->all();
            $penerimaOneSignal = $personil->ambilIdUserByKesatuan($kesatuanTujuan);
            $penerima = $personil->ambilTokenByKesatuan($kesatuanTujuan);
        }
        
        if (env('USE_ONESIGNAL')) { $this->kirimNotifikasiViaOnesignal('kejadian-luar-biasa-baru', $broadcast, collect($penerimaOneSignal)->all()); }
        $this->kirimNotifikasiViaGcm('kejadian-luar-biasa-baru', $broadcast, collect($penerima)->all());
        //End broadcast
        
        return response()->json(['success' => true, 'id' => $plb->id]);
    }

    

    public function show(Request $request, Plb $plb){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil']))
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($plb)
            ->transformWith(PlbTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function getKesatuanTujuan(Request $request)
    {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['personil'])){
            return response()->json(['error' => 'Terlarang'], 403);
        }

        $kesatuan = Kesatuan::filterPoldaPolres()->orderBy('kesatuan', 'ASC')->get();
        return response()->json(['data' => $kesatuan], 200);
    }
}
