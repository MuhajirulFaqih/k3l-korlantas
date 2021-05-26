<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KomentarTransformer;
use App\Transformers\PengaduanTransformer;
use App\Events\KomentarPengaduanEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PengaduanController extends Controller
{
    public function getAll(Request $request){
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan', 'masyarakat']))
            return response()->json(['error' => 'Terlarang'], 403);

        list($orderBy, $dir) = explode(':', $request->sort);

        $limit = $request->limit != '' ? $request->limit : 10;
        if($limit == 0)
            return null;
        
        $pengaduan = $request->filter == '' ?
        Pengaduan::with(['user'])->filterJenisPemilik($user)
                        ->orderBy($orderBy, $direction) :
        Pengaduan::with(['user'])->filterJenisPemilik($user)
                        ->filter($request->filter)
                        ->orderBy($orderBy, $direction);
        
        $paginate = $pengaduan->paginate($limit);
        $collection = $paginate->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(PengaduanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginate))
            ->respond();
    }

    public function lihat(Request $request, Pengaduan $pengaduan){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'masyarakat']) || ($user->jenis_pemilik == 'masyarakat' && $pengaduan->id_user != $user->id))
            return response()->json(['error' => 'Terlarang'], 403);


        return fractal()
            ->item($pengaduan)
            ->transformWith(PengaduanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function lihatKomentar(Request $request, Pengaduan $pengaduan){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'masyarakat']) || ($user->jenis_pemilik == 'masyarakat' && $pengaduan->id_user != $user->id))
            return response()->json(['error' => 'Terlarang'], 403);

        $paginator = $pengaduan->komentar()->orderBy('created_at', 'DESC')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->transformWith(KomentarTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function buatKomentar(Request $request, Pengaduan $pengaduan){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'masyarakat']) || ($user->jenis_pemilik == 'masyarakat' && $pengaduan->id_user != $user->id))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'komentar' => 'required|min:3'
        ]);

        $komentar = $pengaduan->komentar()->create([
            'komentar' => $validData['komentar'],
            'id_user' => $user->id
        ]);

        if (!$komentar)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        $this->broadcastNotifikasi($user, $komentar);
        
        $fractal = fractal()
            ->item($komentar)
            ->transformWith(KomentarTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class);
        // Broadcast to monit
        $data = $fractal->toArray();

        if ($user->jenis_pemilik !== 'admin')
            event(new KomentarPengaduanEvent($data['data']));

        return $fractal->respond();
    }

    public function tambah(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['masyarakat', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $validateData = $request->validate([
            'keterangan' => 'required|min:3',
            'lokasi' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'foto' => 'required|file',
        ]);

        $foto = $validateData['foto']->storeAs(
            'pengaduan',
            Str::random(40).'.'.$validateData['foto']->extension()
        );

        $pengaduan = Pengaduan::create([
            'id_user' => $user->id,
            'id_kesatuan' =>$user->pemilik->id_kesatuan ?? null,
            'keterangan' => $validateData['keterangan'],
            'lokasi' => $validateData['lokasi'],
            'lat' => $validateData['lat'],
            'lng' => $validateData['lng'],
            'foto' => $foto
        ]);

        if (!$pengaduan)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        //$this->broadcastNotifikasi($user, $pengaduan);

        $data = fractal()
            ->item($pengaduan)
            ->transformWith(PengaduanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        event(new PengaduanEvent($data['data']));

        return response()->json(['success' => true]);
    }
}
