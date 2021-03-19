<?php

namespace App\Http\Controllers\API;

use App\Models\Admin;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\AdminTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminController extends Controller
{
    function getVisible(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'personil')
            return response()->json(['error' => 'Terlarang...'], 403);

        $paginator = Admin::where('visible', true)->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(AdminTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->parseIncludes('auth')
            ->respond();
    }
}
