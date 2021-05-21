<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Jenis;
use App\Http\Controllers\Controller;
use App\Transformers\JenisTransformer;
use App\Serializers\DataArraySansIncludeSerializer;

class JenisController extends Controller
{
    public function index()
    {
    	$user = request()->user();
    	if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses '], 403);

        $jenis = Jenis::all();

        if (count($jenis) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        return fractal()
                ->collection($jenis)
                ->transformWith(new JenisTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->respond();
    }
}
