<?php

namespace App\Http\Controllers\API;

use App\Models\TempatVital;
use Illuminate\Http\Request;
use App\Transformers\TempatVitalTransformer;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TempatVitalController extends Controller
{
    public function getByJenis($id)
    {
    	$user = request()->user();
    	if (!in_array($user->jenis_pemilik, ['admin','personil','masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki akses '], 403);

        $tempat = TempatVital::where('id_jenis', $id)->get();

        if (count($tempat) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        return fractal()
                ->collection($tempat)
                ->transformWith(new TempatVitalTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->respond();
    }

    public function getKantor(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil', 'kesatuan', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        $lat = $request->lat;
        $lng = $request->lng;

        $paginator = TempatVital::select('tempat_vital.*',(new TempatVital())->kolomHitungJarak($lat, $lng))->where('id_jenis', 3)->orderBy('jarak', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->transformWith(TempatVitalTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function getAll(Request $request)
    {
        $user = request()->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses '], 403);

        $tempat = TempatVital::whereIn('id_jenis', $request->jenis)->get();

        if (count($tempat) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        return fractal()
                ->collection($tempat)
                ->transformWith(new TempatVitalTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->respond();
    }
}
