<?php

namespace App\Http\Controllers\API;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\JabatanTransformer;

class JabatanController extends Controller
{
    public function ambilSemua(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $jabatan = Jabatan::get();

        return fractal()
            ->collection($jabatan)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(JabatanTransformer::class)
            ->respond();
    }
}
