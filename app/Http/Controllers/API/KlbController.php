<?php

namespace App\Http\Controllers\API;

use App\Models\Klb;
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

    public function store(Request $request){
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

        $data = fractal()
                ->item($klb)
                ->transformWith(KlbTransformer::class)
                ->serializeWith(DataArraySansIncludeSerializer::class)
                ->toArray();
        
        //Todo broadcast

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
