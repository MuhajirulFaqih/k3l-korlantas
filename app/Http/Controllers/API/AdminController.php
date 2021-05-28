<?php

namespace App\Http\Controllers\API;

use App\Models\Admin;
use App\Models\Kesatuan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\AdminTransformer;
use App\Transformers\KesatuanTransformer;
use App\Transformers\ResponseUserTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
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

        Artisan::call("admin:online");

        $id_kesatuan = Kesatuan::ancestorsAndSelf($user->pemilik->id_kesatuan)->pluck('id')->all();
        $collection = Kesatuan::with('auth')->whereIn('id', $id_kesatuan)->has('auth')->paginate(10);


        return fractal()
            ->collection($collection)
            ->parseIncludes(['auth'])
            ->transformWith(KesatuanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
