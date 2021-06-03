<?php

namespace App\Http\Controllers\API;

use App\Models\Pangkat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PangkatTransformer;

class PangkatController extends Controller
{
    public function ambilSemua(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $pangkat = Pangkat::get();

        return fractal()
            ->collection($pangkat)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(PangkatTransformer::class)
            ->respond();
    }
}
