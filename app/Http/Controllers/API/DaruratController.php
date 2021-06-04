<?php

namespace App\Http\Controllers\API;

use App\Events\DaruratSelesaiEvent;
use App\Models\Darurat;
use App\Models\Personil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\DaruratEvent;
use App\Transformers\DaruratTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class DaruratController extends Controller
{
    public function tambah(Request $request){
        $validatedData = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'acc' => 'required|numeric'
        ]);

        $user = $request->user();

        $data = [
            'id_user' => $user->id,
            'id_kesatuan' => $user->pemilik->id_kesatuan ?? null,
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'acc' => $validatedData['acc']
        ];

        $darurat = Darurat::create($data);

        if(!$darurat)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        $personilTerdekat = (new Personil())->terdekat($request->lat, $request->lng);

        foreach ($personilTerdekat as $row){
            $darurat->nearby()->create([
                'id_personil' => $row['id'],
                'lat' => $row['lat'],
                'lng' => $row['lng']
            ]);
        }

        //Broadcast to monit
        $data = fractal()
                ->item($darurat)
                ->transformWith(DaruratTransformer::class)
                ->serializeWith(DataArraySansIncludeSerializer::class)
                ->toArray();

        broadcast(new DaruratEvent($data['data']));

        return response()->json(['success' => true, 'id' => $darurat->id]);
    }

    public function selesai(Request $request, Darurat $darurat){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        if ($user->jenis_pemilik == 'masyarakat' && $user->id != $darurat->id_user)
            return response()->json(['error' => 'Terlarang'], 403);


        if(!$darurat->update(['selesai' => true]))
            return response()->json(['error' => 'Terjadi kesalahan'], 500);


        if ($user->jenis_pemilik == 'admin' || $user->jenis_pemilik == 'kesatuan')
            broadcast(new DaruratSelesaiEvent($darurat));
        else
            $this->kirimNotifikasiViaGcm('darurat-selesai', $darurat->toArray(), [$darurat->user->fcm_id]);

        return response()->json(['success' => true]);
    }

    public function lihatSemua(Request $request){
        if($request->sort)
            list($orderBy, $direction) = explode(':', $request->sort);
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        $darurat = $request->filter == '' ?
        Darurat::with(['user'])->filterJenisPemilik($user)
                        ->orderBy($orderBy, $direction) :
        Darurat::with(['user'])->filterJenisPemilik($user)
                        ->filter($request->filter, $request->status, $request->statusKejadian)
                        ->orderBy($orderBy, $direction);

        $paginator = $darurat->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(DaruratTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function lihat(Request $request, Darurat $darurat){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'personil', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        if ($user->jenis_pemilik == 'masyarakat' && $user->id !== $darurat->id_user)
            return response()->json(['error' => 'Terlarang'], 403);
        return fractal()
            ->item($darurat)
            ->transformWith(DaruratTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
