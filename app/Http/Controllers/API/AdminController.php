<?php

namespace App\Http\Controllers\API;

use App\Models\Admin;
use App\Models\Kesatuan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\AdminTransformer;
use App\Transformers\ResponseUserTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        $paginator = Admin::visible()->paginate(10);
        $collection = $paginator->getCollection();


        return fractal()
            ->collection($collection)
            ->transformWith(AdminTransformer::class)
            ->parseIncludes('auth')
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function getKesatuan(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'personil')
            return response()->json(['error' => 'Terlarang'], 403);

        $id_kesatuan = Kesatuan::ascendantsAndSelf($user->pemilik->id_kesatuan)->pluck('id')->all();
        $collection = Kesatuan::whereIn('id', $id_kesatuan)->paginate(10);


        return fractal()
            ->collection($collection)
            ->transformWith(UserTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
