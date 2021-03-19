<?php

namespace App\Http\Controllers\API;

use App\Serializers\DataArraySansIncludeSerializer;
use App\Models\TigaPilar;
use App\Transformers\TigaPilarTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TigaPilarController extends Controller
{
    public function getAll(Request $request){
         $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
             return response()->json(['error' => 'Anda tidak memiliki akses pada halaman ini'], 403);

        $tigapilarAll = env('TIGAPILAR_OPEN') ? null : ($user->jenis_pemilik == 'bhabin' ? $user->pemilik->kelurahan->pluck('id_kel')->all() : null);

        $paginator = $tigapilarAll ? TigaPilar::perKelurahan($request->filter, $tigapilarAll)->paginate(10) : TigaPilar::filtered($request->filter, $request->id_kec, $request->id_kel)->paginate(10);
        $collection = $paginator->getCollection();

         return fractal()
             ->collection($collection)
             ->transformWith(TigaPilarTransformer::class)
             ->paginateWith(new IlluminatePaginatorAdapter($paginator))
             ->serializeWith(DataArraySansIncludeSerializer::class)
             ->respond();
    }

    public function tambah(Request $request){
        $user = $request->user();

        if($user->jenis_pemilik != 'bhabin' && !in_array($request->id_deskel, $user->pemilik->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
           'nama' => 'required|min:3',
           'no_telp' => 'required',
           'jabatan' => 'required|min:3',
           'foto' => 'required|image',
            'id_deskel' => 'required'
        ]);

        $validData['foto'] = $validData['foto']->storeAs(
            'tiga_pilar', str_random(40).'.'.$validData['foto']->extension()
        );

        $tigaPilar = TigaPilar::create($validData);

        if (!$tigaPilar)
            return response()->json(['error' => 'Terjadi kesalahan']);

        return response()->json(['success' => true, 'id' => $tigaPilar->id]);
    }

    public function update(Request $request, TigaPilar $tigaPilar){
        $user = $request->user();

        if($user->jenis_pemilik != 'bhabin' && !in_array($tigaPilar->id_deskel, $user->pemilik->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'nama' => 'required|min:3',
            'no_telp' => 'required',
            'jabatan' => 'required|min:3',
            'foto' => 'nullable|image',
        ]);

        $updateData = [
            'nama' => $validData['nama'],
            'no_telp' => $validData['no_telp'],
            'jabatan' => $validData['jabatan']
        ];


        if (isset($validData['foto'])){
            $updateData['foto'] = $validData['foto']->storeAs(
                'tiga_pilar', str_random(40).'.'.$validData['foto']->extension()
            );
        }

        if (!$tigaPilar->update($updateData))
            return response()->json(['error' => 'Terjadi kesalahan'], 500);


        return response()->json(['success' => true]);
    }

    public function delete(Request $request, TigaPilar $tigaPilar){
        $user = $request->user();

        if($user->jenis_pemilik != 'bhabin' && !in_array($tigaPilar->id_deskel, $user->pemilik->kelurahan->pluck('id_kel')->all()))
            return response()->json(['error' => 'Terlarang'], 403);

        if (!$tigaPilar->delete())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }
}
