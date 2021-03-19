<?php

namespace App\Http\Controllers\API;

use App\Models\TempatVital;
use Illuminate\Http\Request;
use App\Transformers\TempatVitalTransformer;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;

class TempatVitalController extends Controller
{
    public function getByJenis($id)
    {
    	$user = request()->user();
    	if (!in_array($user->jenis_pemilik, ['admin']))
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
