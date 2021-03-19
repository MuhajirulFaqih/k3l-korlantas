<?php

namespace App\Http\Controllers\API;

use App\Models\BelanjaBidang;
use App\Models\GiatDanaDesa;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use App\Models\Pendapatan;
use App\Models\ProfilKelurahan;
use App\Models\RincianBelanja;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\BelanjaBidangTransformer;
use App\Transformers\GiatDanaDesaTransformer;
use App\Transformers\KelurahanTransformer;
use App\Transformers\PendapatanTransformer;
use App\Transformers\ProfileDesaTransformer;
use App\Transformers\RincianBelanjaTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ProfilKelurahanController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['bhabin', 'personil']))
            return response()->json(['error' => 'Terlarang'], 403);

        $profile = ProfilKelurahan::where('id_kel', $request->kelurahan)->first();

        if (!$profile)
            return response()->json(['data' => null]);


        return fractal()
            ->item($profile)
            ->transformWith(new ProfileDesaTransformer())
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function updateCreate(Request $request)
    {

        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);


        $payload = $request->validate([
            'id_kel' => 'required',
            'kades' => 'required',
            'sekdes' => 'required',
            'luas_daerah' => 'required',
            'satuan_luas' => 'required',
            'jumlah_penduduk' => 'required',
            'batas_utara' => 'required',
            'batas_selatan' => 'required',
            'batas_timur' => 'required',
            'batas_barat' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'zoom' => 'required',
        ]);

        $profile = ProfilKelurahan::updateOrCreate(['id_kel' => $payload['id_kel']], $payload);

        if (!$profile)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);


        return response()->json(['success' => true]);
    }

// start Pendapatan

    public function createPendapatan(Request $request)
    {
        $user = $request->user();

        // Hanya bhabin yang memiliki akses
        if ($user->jenis_pemilik != 'bhabin')
            return response()->json(['error' => "Terlarang"], 403);

        $payload = $this->validasiPendapatan($request);

        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if (!in_array($payload['id_kel'], $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => "Terlarang"], 403);

        $insert = Pendapatan::create($payload);

        if (!$insert)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);

    }

    public function deletePendapatan(Request $request, Pendapatan $pendapatan){
        $user = $request->user();

        // Hanya bhabin yang memiliki akses
        if ($user->jenis_pemilik != 'bhabin' && !in_array($pendapatan->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => "Terlarang"], 403);

        if ($pendapatan->delete())
            return response()->json(['success' => true]);

        return response()->json(['error' => 'Terjadi kesalahan'], 500);
    }

    public function updatePendapatan(Request $request, Pendapatan $pendapatan)
    {
        $user = $request->user();


        // Hanya bhabin yang memiliki akses
        if ($user->jenis_pemilik != 'bhabin')
            return response()->json(['error' => "Terlarang"], 403);

        $payload = $this->validasiPendapatan($request);

        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if (!in_array($payload['id_kel'], $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => "Terlarang"], 403);

        $update = $pendapatan->update($payload);

        if (!$update)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function delete(Request $request, Pendapatan $pendapatan){

    }

    public function pendapatan(Request $request)
    {
        $user = $request->user();

        // Hanya bhabin dan admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => "Terlarang"], 403);
        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if($user->jenis_pemilik == 'bhabin') {
            if (!in_array($request->kelurahan, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
                return response()->json(['error' => "Terlarang"], 403);
        }

        $pendapatan = Pendapatan::where('id_kel', $request->kelurahan)->orderBy('created_at', 'desc');


        /*if ($pendapatan->count() == 0)
            return response()->json(['tidak ada data'], 204);*/

        $paginator = $pendapatan->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new PendapatanTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function detailPendapatan(Request $request)
    {
        $pendapatan = Pendapatan::where('id_kel', $request->kelurahan)->get();

        return fractal()
            ->collection($pendapatan)
            ->transformWith(new PendapatanTransformer())
            ->toArray();
    }

// End Pendapatan

// start Belanja Bidang

    public function belanjaBidang(Request $request)
    {
        $user = $request->user();

        // Hanya bhabin dan admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => "Terlarang"], 403);


        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if($user->jenis_pemilik == 'bhabin') {
            if (!in_array($request->kelurahan, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
                return response()->json(['error' => "Terlarang"], 403);
        }


        $paginator = BelanjaBidang::where('id_kel', $request->kelurahan)->orderBy('created_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->transformWith(new BelanjaBidangTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->respond();
    }

    public function detailBelanjaBidang(Request $request, BelanjaBidang $belanja)
    {
        // Todo Hanya bhabin dengan desa yg ditunjuk yg dapat aksess

        return fractal()
            ->item($belanja)
            ->transformWith(new BelanjaBidangTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->toArray();
    }

    public function createBelanjaBidang(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        $data = $this->validasiBelanjaBidang($request);

        $insert = BelanjaBidang::create($data);

        if (!$insert)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function updateBelanjaBidang(Request $request, BelanjaBidang $belanja)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array(optional($belanja)->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        $data = $this->validasiBelanjaBidang($request);

        $update = $belanja->update($data);

        if (!$update)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function deleteBelanjaBidang(Request $request, BelanjaBidang $belanja)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array(optional($belanja)->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        if(!$belanja->delete())
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);
    }
    
    // end Belanja Bidang


    // start RIncian Belanja
    public function RincianBelanja(Request $request)
    {
        $user = $request->user();
        // Hanya bhabin dan admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin'] ))
            return response()->json(['error' => "Terlarang"], 403);

        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if($user->jenis_pemilik == 'bhabin') {
            if (!in_array($request->kelurahan, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
                return response()->json(['error' => "Terlarang"], 403);
        }

        $paginator = RincianBelanja::where('id_kel', $request->kelurahan)->orderBy('created_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new RincianBelanjaTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function detailRincianBelanja(Request $request)
    {
        $data = RincianBelanja::find($request->id);

        return fractal()
            ->item($data)
            ->transformWith(new RincianBelanjaTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->toArray();
    }

    public function createRincianBelanja(Request $request)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        $data = $this->validasiRincianBelaja($request);

        $insert = RincianBelanja::create($data);

        if (!$insert)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function updateRincianBelanja(Request $request, RincianBelanja $rincianBelanja)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($request->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        $insert = $rincianBelanja->update($this->validasiRincianBelaja($request));

        if (!$insert)
            return response()->json(['error' => 'Terjadi Kesalahan'], 500);

        return response()->json(['success' => true]);
    }

    public function deleteRincianBelanja(Request $request, RincianBelanja $rincian)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($rincian->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => "Terlarang"], 403);

        if($rincian->delete())
            return response()->json(['success' => true]);

        return response()->json(['error' => 'Terjadi kesalahan'], 500);
    }

    // end Rincian Belanja
    // start Giat Dana Desa

    public function createGiatDanaDesa(Request $request)
    {
        $validatedData = $this->validasiGiatDanaDesa($request);

        $foto = $validatedData['foto']
            ->storeAs(
                'GiatDanaDesa',
                str_random(40) . '.'
                    . $request->file('foto')
                    ->extension()
            );

        $data = array(
            'id_kel' => $request->id_kel,
            'giat' => $request->giat,
            'keterangan' => $request->keterangan,
            'biaya' => $request->biaya,
            'foto' => $foto,
        );

        $insert = GiatDanaDesa::create($data);

        if (!$insert)
            return response()->json(['error' => 'Terjadi Kesalahan']);

        return response()->json(['success' => true]);

    }

    public function detailGiatDanaDesa(Request $request, GiatDanaDesa $giatdesa)
    {
        $data = $giatdesa->find($request->id);

        return fractal()
            ->item($data)
            ->transformWith(new GiatDanaDesaTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->toArray();

    }

    public function GiatDanaDesa(Request $request)
    {
        $user = $request->user();

        // Hanya bhabin dan admin yang memiliki akses
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => "Terlarang"], 403);
        // Hanya bhabin yang memiliki akses pada kelurahan yang terpilih
        if($user->jenis_pemilik == 'bhabin') {
            if (!in_array($request->kelurahan, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
                return response()->json(['error' => "Terlarang"], 403);
        }

        $paginator = GiatDanaDesa::where('id_kel', $request->kelurahan)->orderBy('created_at', 'desc')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new GiatDanaDesaTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->serializeWith(new DataArraySansIncludeSerializer())
            ->respond();
    }

    public function deleteGiatDanaDesa(Request $request, GiatDanaDesa $danaDesa)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($danaDesa->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        if($danaDesa->delete())
            return response()->json(['success' => true]);

        return response()->json(['error' => 'Terjadi kesalahan'], 500);
    }

    public function updateGiatDanaDesa(Request $request, GiatDanaDesa $danaDesa)
    {
        $user = $request->user();

        if ($user->jenis_pemilik != 'bhabin' && !in_array($danaDesa->id_kel, $user->pemilik->kelurahan->pluck('id_kel')->toArray()))
            return response()->json(['error' => 'Terlarang'], 403);

        $validData = $request->validate([
            'id_kel' => 'required',
            'giat' => 'required',
            'keterangan' => 'required',
            'biaya' => 'required',
            'foto' => 'nullable|image'
        ]);

        $updateData = [
            'id_kel' => $validData['id_kel'],
            'giat' => $validData['giat'],
            'keterangan' => $validData['keterangan'],
            'biaya' => $validData['biaya']
        ];

        if ($request->foto){
            $foto = $validData['foto']->storeAs(
                'GiatDanaDesa', str_random(40).'.'.$validData['foto']->extension()
            );

            $updateData['foto'] = $foto;
        }

        if(!$danaDesa->update($updateData))
            return response()->json(['error' => 'Terjadi kesalahan'], 500);

        return response()->json(['success' => true]);

    }
    // end Giat Dana Desa

    public function validasiGiatDanaDesa($request)
    {
        $payload = $request->validate([
            'id_kel' => 'required',
            'giat' => 'required',
            'keterangan' => 'required',
            'foto' => 'required|image',
            'biaya' => 'required',
        ]);

        return $payload;
    }

    public function validasiRincianBelaja($request)
    {
        $payload = $request->validate([
            'id_kel' => 'required',
            'uraian' => 'required',
            'jumlah' => 'required',
            'tahun_anggaran' => 'required',
        ]);

        return $payload;
    }

    public function validasiBelanjaBidang($request)
    {
        $payload = $request->validate([
            'id_kel' => 'required',
            'penyelenggaraan' => 'required',
            'pelaksanaan' => 'required',
            'pemberdayaan' => 'required',
            'pembinaan' => 'required',
            'tak_terduga' => 'required',
            'tahun_anggaran' => 'required',
        ]);

        return $payload;
    }

    public function validasiPendapatan($request)
    {
        $data = $request->validate([
            'bagihasilpajakdaerah' => 'required',
            'pendapatanaslidaerah' => 'required',
            'alokasidanadesa' => 'required',
            'silpa' => 'required',
            'tahun_anggaran' => 'required',
            'id_kel' => 'required',
        ]);

        return $data;
    }

    public function getDesa(Request $request)
    {
       $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kelurahan = $request->filter == '' ?
            Kelurahan::getByProv(env('PROVINSI_DANA_DESA', '64'))->orderBy($orderBy, $direction) :
            Kelurahan::filteredDanaDesa($request->filter, env('PROVINSI_DANA_DESA', '64'))
            ->orderBy($orderBy, $direction);

        if (count($kelurahan->get()) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        $limit = $request->limit != '' ? $request->limit : 10;
        $paginator = $kelurahan->paginate($limit);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes(['profil', 'kecamatan', 'kecamatan.kabupaten'])
            ->transformWith(new KelurahanTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }
}
