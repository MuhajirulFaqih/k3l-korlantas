<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\JabatanTransformer;

class JabatanController extends Controller
{
    public function ambilSemua(Request $request){
        $user = $request->user();

        if($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $jabatan = Jabatan::get();

        return fractal()
            ->collection($jabatan)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(JabatanTransformer::class)
            ->respond();
    }
}
