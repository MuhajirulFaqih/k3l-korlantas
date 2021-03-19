<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\InformasiTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\Informasi;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class InformasiController extends Controller
{
    public function getAktif(Request $request){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'bhabin', 'personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess pada halaman ini'], 403);

        $active = Informasi::active()->get();

        return fractal()
            ->collection($active)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(InformasiTransformer::class)
            ->respond();
    }
    public function getAll(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'bhabin', 'personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess pada halaman ini'], 403);

        $paginator = Informasi::orderBy('aktif', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(InformasiTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function tambah(Request $request){
        $user = $request->user();

        // Hanya admin yang memiliki akses
        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validatedData = $request->validate([
            'info' => 'required|min:3'
        ]);

        $informasi = Informasi::create([
            'informasi' => $validatedData['info'],
            'aktif' => true
        ]);

        if (!$informasi)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true], 201);
    }

    public function ubahInformasi(Request $request, Informasi $informasi){
        $user = $request->user();

        // Hanya admin yang memiliki akses
        if ($user->jenis_pemilik !== 'admin')
            return response()->json(['error' => 'Terlarang'], 403);
        
        $validatedData = $request->validate([
            'aktif' => 'required'
        ]);

        $informasi->aktif = $validatedData['aktif'] == 'true' ? 0 : 1;

        if(!$informasi->save())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        

        return response()->json(['success' => true, $informasi]);
    }
}
