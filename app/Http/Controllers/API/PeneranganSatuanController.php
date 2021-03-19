<?php

namespace App\Http\Controllers\API;

use App\Models\PeneranganSatuan;
use App\Http\Controllers\Controller;
use App\Models\PeneranganSatuanFile;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PeneranganSatuanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class PeneranganSatuanController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);

        list($orderBy, $direction) = explode(':', $request->sort ?? 'created_at:desc');

        $paginator = PeneranganSatuan::filtered($request->filter)->orderBy($orderBy, $direction)->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(PeneranganSatuanTransformer::class)
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
            'jenis' => 'required|in:video,image,pdf',
            'files.*' => 'required'
        ]);


        $peneranganSatuan = PeneranganSatuan::create(['judul' => $validData['judul']]);

        if (!$peneranganSatuan)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        foreach ($validData['files'] as $row){
            if ($validData['jenis'] == 'video'){
                preg_match('/v=([a-zA-Z0-9\_\-]+)&?/m', $row, $matches);
                $peneranganSatuan->files()->create([
                    'file' => $row,
                    'thumbnails' => 'http://img.youtube.com/vi/'.$matches[1].'/hqdefault.jpg',
                    'type' => $validData['jenis'],
                    'video_id'=> $matches[1]
                ]);

            } else {
                $file = $row->storeAs('penerangan_satuan', Str::random(40).'.'.$row->extension());

                $peneranganSatuan->files()->create([
                    'file' => $file,
                    'type' => $validData['jenis'],
                    'file_name' => $row->getClientOriginalName()
                ]);
            }
        }

        // Todo Kirim notifikasi
        $data = [
            'id' => $peneranganSatuan->id,
            'nama' => $user->pemilik->nama,
            'pesan' => 'Penerangan Satuan Baru'
        ];

        $penerima = $this->personil->ambilToken();
        $penerima = $penerima->merge($this->bhabin->ambilToken())->all();

        $this->kirimNotifikasiViaGcm('penerangan-satuan', $data, $penerima);

        if (env("USE_ONESIGNAL", false)){
            $penerimaOneSignal = $this->personil->ambilId();
            $penerimaOneSignal = $penerimaOneSignal->merge($this->bhabin->ambilId())->all();

            $this->kirimNotifikasiViaOnesignal('penerangan-satuan', $data, $penerimaOneSignal);
        }

        return response()->json(['success' => true]);
    }

    public function delete(Request $request, PeneranganSatuan $peneranganSatuan){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $file = $peneranganSatuan->files()->delete();
        $penerangan = $peneranganSatuan->delete();

        if (!$file || !$penerangan)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }



    public function get(Request $request, PeneranganSatuan $peneranganSatuan){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin']))
            return response()->json(['error' => 'Terlarang'], 403);

        return fractal()
            ->item($peneranganSatuan)
            ->transformWith(PeneranganSatuanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function upload(Request $request){
        $user = $request->user();

        if ($user->jenis_pemilik != 'admin')
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'type' => 'required',
            'files' => 'required'
        ]);

        if ($validData['type'] == 'video'){
            preg_match('/v=([a-zA-Z0-9\_\-]+)&?/m', $validData['file'], $matches);
            $peneranganSatuanFile = PeneranganSatuanFile::create([
                'file' => $validData['file'],
                'thumbnails' => 'http://img.youtube.com/vi/'.$matches[1].'/hqdefault.jpg',
                'type' => $validData['jenis'],
            ]);

        } else {
            $file = $validData['files']->storeAs('penerangan_satuan', Str::random(40).'.'.$validData['files']->extension());

            $peneranganSatuanFile = PeneranganSatuanFile::create(['file' => $file, 'type' => $validData['type']]);
        }

        if (!$peneranganSatuanFile)
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true, 'data' => ['id' => $peneranganSatuanFile->id, 'name' => $validData['files']->getClientOriginalName()]]);
    }
}
