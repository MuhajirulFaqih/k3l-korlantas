<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sispammako;
use GuzzleHttp\Client as GuzzleClient;
use App\Transformers\SispammakoTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class SispammakoController extends Controller
{
    public function getAll(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess'], 403);

        $paginator = Sispammako::filtered()->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(SispammakoTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function tambah(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        if(in_array($user->jenis_pemilik, ['personil']) && !$user->pemilik->jabatan->sispammako)
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $validatedData = $request->validate([
            'jenis' => 'required',
            'arahan' => 'nullable'
        ]);

        $sispammako = Sispammako::create([
            'id_user' => $user->id,
            'jenis' => $validatedData['jenis'],
            'dokumen' => config('pengaturan.pdf_sispammako'),
            'arahan' => $validatedData['arahan'] ?? ''
        ]);

        if (!$sispammako)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        //Todo notifikasi
        $penerima = $this->personil->ambilTokenLain($user->pemilik->id);
        $penerima = $penerima->merge($this->bhabin->ambilToken())->all();

        $pengirim = $user->jenis_pemilik == 'admin' ? $user->pemilik->nama : $user->pemilik->jabatan->jabatan;
        $data = [
            'id' => $sispammako->id,
            'pengirim' => $pengirim,
            'jenis' => $sispammako->jenis,
            'pesan' => 'Sispammako oleh '.$pengirim
        ];

        $this->kirimNotifikasiViaGcm('sispammako-baru', $data, $penerima);

        if (env('USE_ONESIGNAL', false)){
            $penerimaOneSignal = $this->personil->ambilIdLain($user->pemilik->id);
            $penerimaOneSignal = $penerimaOneSignal->merge($this->bhabin->ambilId())->all();

            $this->kirimNotifikasiViaOnesignal('sispammako-baru', $data, $penerimaOneSignal);
        }

        if ($user->jenis_pemilik != 'admin' && env('URL_SISPAMMAKO'))
            $this->sendSispam($sispammako->jenis);

        return response()->json(['success' => true], 201);
    }

    private function sendSispam($jenis){
        $client = new GuzzleClient(['timeout' => 5.0]);

        $response = $client->request('POST', env('URL_SISPAMMAKO'), ['headers' => ['token' => env('APP_KEY'), 'Accept' => 'application/json', 'Content-Type' => 'application/x-www-form-urlencoded'],'form_params' => ['type' => $jenis]]);

        return $response;
    }

    public function get(Request $request, Sispammako $sispammako){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        return fractal()
            ->item($sispammako)
            ->transformWith(SispammakoTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
