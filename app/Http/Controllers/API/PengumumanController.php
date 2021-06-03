<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kesatuan;
use App\Models\Pengumuman;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PengumumanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PengumumanController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil']))
            return response()->json(['errors' => 'Terlarang'], 403);

        list($orderBy, $direction) = explode(':', $request->sort ?? 'created_at:desc');


        if ($user->jenis_pemilik == 'personil'){
            $personil = $user->pemilik;

            $id_kesatuan = Kesatuan::ancestorsAndSelf($personil->id_kesatuan)->pluck('id')->all();
            $query = Pengumuman::with('kesatuan', 'kesatuan.parent')->whereIn('id_kesatuan', $id_kesatuan);
        } else {
            $query = Pengumuman::with('kesatuan', 'kesatuan.parent');
        }


        $paginator = $query->orderBy($orderBy, $direction)->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(PengumumanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->parseIncludes(['user', 'kesatuan'])
            ->respond();
    }

    public function store(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['errors' => 'Terlarang '], 403);

        $validData = $request->validate([
            'id_kesatuan' => 'nullable|numeric',
            'judul' => 'required|min:3',
            'file' => 'required|file'
        ]);

        $file = $validData['file']->storeAs(
            'pengumuman',
            Str::random(40).'.'.$validData['file']->extension()
        );

        $validData['file'] = $file;
        $validData['id_user'] = $user->id;
        $validData['id_kesatuan'] = $validData['id_kesatuan'] ?? 1;

        $pengumuman = Pengumuman::create($validData);

        if (!$pengumuman)
            return response()->json(['errors' => 'Terjadi kesalahan'], 500);

        $pengumuman = Pengumuman::find($pengumuman->id);

        // Send notifikasi ke handphone
        $this->broadcastNotifikasi($user, $pengumuman);

        return response()->json(['success' => true]);
    }

    public function show(Request $request, Pengumuman $pengumuman){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil']))
            return response()->json(['errors' => 'Terlarang'], 403);

        $personil = $user->pemilik;

        $id_kesatuan = Kesatuan::ancestorsAndSelf($personil->id_kesatuan)->pluck('id')->all();

        if (!in_array($pengumuman->id_kesatuan, $id_kesatuan))
            return response()->json(['errors' => "Terlarang"], 403);

        return fractal()
            ->item($pengumuman)
            ->transformWith(PengumumanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->parseIncludes(['user', 'kesatuan'])
            ->respond();
    }

    public function delete(Request $request, Pengumuman $pengumuman){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['errors' => 'Terlarang'], 403);

        if (!$pengumuman->delete())
            return response()->json(['errors' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true ]);

    }
}
