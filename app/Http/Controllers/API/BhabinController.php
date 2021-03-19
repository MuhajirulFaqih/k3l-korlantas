<?php

namespace App\Http\Controllers\API;

use App\Models\Kelurahan;
use Illuminate\Http\Request;
use App\Transformers\BhabinKelurahanTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class BhabinController extends Controller
{
    public function getDesa(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['bhabin', 'personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        if ($user->jenis_pemilik == 'bhabin')
            $desa = $user->pemilik->kelurahan();
        else
            $desa = Kelurahan::semua(explode(',', env('APP_KAB')))->filteredBhabin($request->filter, $request->id_kab, $request->id_kec);


        $pagination = $desa->paginate(10);
        $collection = $pagination->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(BhabinKelurahanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($pagination))
            ->respond();
    }
}
