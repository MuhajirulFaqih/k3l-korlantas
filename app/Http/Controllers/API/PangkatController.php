<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pangkat;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PangkatTransformer;

class PangkatController extends Controller
{
    public function ambilSemua(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $pangkat = Pangkat::get();

        return fractal()
            ->collection($pangkat)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(PangkatTransformer::class)
            ->respond();
    }
}
