<?php

namespace App\Http\Controllers\API;

use App\Exports\AbsensiExport;
use App\Models\Absensi;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\AbsensiTransformer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses di halaman ini'], 403);

        $paginator = $request->id_kesatuan == '' &&
        			$request->rentangTanggal[0] == '' &&
        			$request->nrp == '' ?
            		Absensi::orderBy($orderBy, $direction)->paginate(10) :
            		Absensi::filtered($request->id_kesatuan, $request->rentangTanggal, $request->nrp)
		            ->orderBy($orderBy, $direction)
		            ->paginate(10);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(new AbsensiTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function absenPersonil(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $validatedData = $request->validate([
            'lokasi' => 'required',
            'kondisi' => 'required',
            'dokumentasi' => 'required|image',
            'lat' => 'required',
            'lng' => 'required'
        ]);
        $personil = $user->pemilik;

        $tanggal = date('Y-m-d');

        $dokumentasi = null;

        if (isset($validatedData['dokumentasi'])) {
            $dokumentasi = $validatedData['dokumentasi']
                ->storeAs('absensi', $user->id . '_' . Str::random(40) . '.' .
                    $validatedData['dokumentasi']->extension());
        }
        //Jika belum ditemukan, absen datang
         //Cek jika hari baru
         $cekHariBaru = Absensi::whereDate('waktu_mulai', $tanggal)
         ->where('id_personil', $personil->id);
        if($cekHariBaru->count() == 0) {
            $this->absenDatang($personil, $dokumentasi, $request);
        } else {
            if($cekHariBaru->first()->waktu_selesai != null) {
                return response()->json(['error' => 'Data absensi hari ini telah ditambahkan'], 422);        
            }
            $this->absenPulang($personil, $dokumentasi, $request);
        }
        
        return response()->json(['success' => true], 201);
    }

    public function absenDatang($personil, $dokumentasi, $request)
    {
        Absensi::create([
            'id_personil' => $personil->id,
            'waktu_mulai' => \Carbon\Carbon::now(),
            'waktu_selesai' => null,
            'lat_datang' => $request['lat'],
            'lng_datang' => $request['lng'],
            'lokasi_datang' => $request['lokasi'],
            'kondisi_datang' => $request['kondisi'],
            'dokumentasi_datang' => $dokumentasi,
        ]);
    }

    public function absenPulang($personil, $dokumentasi, $request)
    {
        $tanggal = date('Y-m-d');

        $absensi = Absensi::whereDate('waktu_mulai', $tanggal)
            ->where('id_personil', $personil->id)
            ->first();

        if(is_null($absensi->waktu_selesai)) {
            $absensi->update([
                'waktu_selesai' => \Carbon\Carbon::now(),
                'lat_pulang' => $request['lat'],
                'lng_pulang' => $request['lng'],
                'lokasi_pulang' => $request['lokasi'],
                'kondisi_pulang' => $request['kondisi'],
                'dokumentasi_pulang' => $dokumentasi,
            ]);
        }
    }

    public function getToday(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess di halaman ini'], 403);

        $absensi = Absensi::whereDate('waktu_mulai', date('Y-m-d'))
                ->where('id_personil', $user->pemilik->id);  
        if($absensi->count() == 0) 
            return response()->json(['message' => 'Tidak ada content.'], 204);

        return fractal()
            ->item($absensi->first())
            ->transformWith(new AbsensiTransformer())
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->respond();
    }

    public function export(Request $request)
    {
    	$user = $request->user();
    	if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses di halaman ini'], 403);

        $data =  Absensi::filtered($request->kesatuan,
    								$request->tanggal,
    								$request->nrp)
	            ->orderBy('id_personil', 'DESC')
	            ->get();

	    return Excel::download(new AbsensiExport($data), 'Document.xlsx');
    }
}
