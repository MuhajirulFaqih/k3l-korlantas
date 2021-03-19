<?php

namespace App\Http\Controllers\API;

use App\Models\BukuSaku;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\BukuSakuTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class BukuSakuController extends Controller
{
    public function getAll(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $paginator = BukuSaku::paginate(10);
        $collector = $paginator->getCollection();

        return fractal()
            ->collection($collector)
            ->transformWith(BukuSakuTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();

    }

    public function tambah(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'judul' => 'required',
            'file' => 'required'
        ]);


        $file = $validData['file']->storeAs(
            'buku_saku', str_random(40).'.'.$validData['file']->extension()
        );


        $bukuSaku = BukuSaku::create(['file' => $file, 'judul' => $validData['judul']]);

        if (!$bukuSaku)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'id' => $bukuSaku->id]);
    }
}
