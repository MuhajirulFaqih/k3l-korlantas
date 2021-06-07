<?php

namespace App\Http\Controllers\API;

use App\Models\Klb;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\KlbTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class KlbController extends Controller
{

    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);
        
        list($orderBy, $direction) = $request->sort != '' ? explode(':', $request->sort) : explode(':', 'created_at:desc');

        $klb = Klb::with(['user'])->filterJenisPemilik($user)
                        ->orderBy($orderBy, $direction);

        $paginator = $klb->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(KlbTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'keterangan' => 'required'
        ]);

        $user = $request->user();

        $data = [
            'id_user' => $user->id,
            'id_kesatuan' => $user->pemilik->id_kesatuan ?? null,
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'keterangan' => $validatedData['keterangan']
        ];

        $klb = Klb::create($data);
        
        //Start broadcast
        $data = [
            'id' => $klb->id,
            'pesan' => $user->nama.' mengirimkan kejadian luar biasa.',
            'nama' => $user->nama
        ];
        $penerimaOneSignal = User::join('personil', 'personil.id', '=', 'user.id_pemilik')
                            ->where('jenis_pemilik', 'personil')->whereIn('id_jabatan', [1, 2, 30])->get()->pluck('id')->all();
        $penerima = User::join('personil', 'personil.id', '=', 'user.id_pemilik')
                            ->where('jenis_pemilik', 'personil')->whereIn('id_jabatan', [1, 2, 30])->whereNotNull('fcm_id')->get()->pluck('fcm_id')->all();
        if (env('USE_ONESIGNAL')) {
            $this->kirimNotifikasiViaOnesignal('kejadian-luar-biasa-baru', $data, collect($penerimaOneSignal)->all());
        }
        $this->kirimNotifikasiViaGcm('kejadian-luar-biasa-baru', $data, collect($penerima)->all());
        //End broadcast
        return response()->json(['success' => true, 'id' => $klb->id]);
    }

    

    public function show(Request $request, Klb $klb){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($klb)
            ->transformWith(KlbTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
