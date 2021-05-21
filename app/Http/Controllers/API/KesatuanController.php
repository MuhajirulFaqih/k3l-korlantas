<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kesatuan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KesatuanTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class KesatuanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses di halaman ini'], 403);

        $paginator = $request->filter == '' ?
            Kesatuan::with('parent')->orderBy($orderBy, $direction)->paginate(10) :
            Kesatuan::with('parent')->filtered($request->filter)
            ->orderBy($orderBy, $direction)
            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new KesatuanTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $validatedData = $request->validate([
            'kesatuan'      => 'required',
            // 'email'             => 'email:required',
            'induk'               => 'required',
            // 'banner'       => 'nullable|image',
        ]);

        // if (isset($validatedData['banner'])) {
        //     $banner = $validatedData['banner']
        //                            ->storeAs('banner', $user->id. '_' . str_random(40).'.'.
        //                            $validatedData['banner']->extension());
        // } else {
        //     $banner = null;
        // }

        $kesatuan = Kesatuan::create([
            'kesatuan' => $request->kesatuan,
            // 'email_polri' => $request->email,
            'induk' => $request->induk,
            // 'banner_grid' => $banner,
        ]);

        if(!$kesatuan) {
            return response()->json(['error' => 'terjadi kesalahan saat menyimpan data'], 500);
        }

    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $validatedData = $request->validate([
            'kesatuan'      => 'required',
            // 'email'         => 'email:required',
            'induk'         => 'required',
            // 'banner'       => 'nullable|image',
        ]);

        $kesatuan = Kesatuan::find($id);

        // if (isset($validatedData['banner'])) {
        //     $banner = $validatedData['banner']
        //                            ->storeAs('banner', $user->id. '_' . str_random(40).'.'.
        //                            $validatedData['banner']->extension());
        //     Storage::delete($kesatuan->banner_grid);
        // }
        // else {
        //     $banner = $kesatuan->banner_grid;
        // }

        $kesatuan->update([
            'kesatuan' => $request->kesatuan,
            // 'email_polri' => $request->email,
            'induk' => $request->induk,
            // 'banner_grid' => $banner,
        ]);

        if(!$kesatuan) {
            return response()->json(['error' => 'terjadi kesalahan saat mengedit data'], 500);
        }
    }

    public function delete($id)
    {
        $kesatuan = Kesatuan::find($id);
        if(!$kesatuan->delete()) {
            return response()->json(['error' => 'terjadi kesalahan saat menghapus data'], 500);
        }
    }

    public function ambilSemua(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kesatuan = Kesatuan::with('parent')->get();
        return fractal()
            ->collection($kesatuan)
            ->transformWith(KesatuanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
