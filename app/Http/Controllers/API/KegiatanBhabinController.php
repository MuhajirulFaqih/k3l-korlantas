<?php

namespace App\Http\Controllers\API;

use App\Models\BidangKegiatanBhabin;
use App\Http\Controllers\Controller;
use App\IndikatorKegiatanBhabin;
use App\Models\JenisKegiatanBhabin;
use App\Models\KategoriKegiatanBhabin;
use App\Models\KegiatanBhabin;
use App\Models\KegiatanMasyarakat;
use App\Models\MasyarakatKegiatanBhabin;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\KegiatanBhabinTransformer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class KegiatanBhabinController extends Controller
{
    public function index(Request $request)
    {
    	$user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kegiatan = $request->filter == '' ?
            KegiatanBhabin::orderBy($orderBy, $direction) :
            KegiatanBhabin::search($request->filter)
            ->orderBy($orderBy, $direction);

        if (count($kegiatan->get()) === 0)
            return response()->json(['message' => 'Tidak ada content.'], 204);

        $limit = $request->limit != '' ? $request->limit : 10;
        if($limit == 0)
            return null;
        $paginator = $kegiatan->paginate($limit);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes(['tipe', 'jenis', 'kategori', 'bidang', 'masyarakat', 'kecamatan'])
            ->transformWith(new KegiatanBhabinTransformer(true))
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function detail(Request $request, $id)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kegiatan = KegiatanBhabin::find($id);

        return fractal()
            ->item($kegiatan)
            ->parseIncludes(['tipe', 'jenis', 'kategori', 'bidang', 'masyarakat', 'kecamatan'])
            ->transformWith(new KegiatanBhabinTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->respond();
    }

    public function create(Request $request)
    {
    	$user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $validatedData = $request->validate([
            'tipe_laporan' => 'required', //Tipe laporan, dds sambang dan lain-lain
            'jenis' => 'nullable',
            'lokasi' => 'nullable',
            'uraian' => 'nullable',
            'kategori' => 'nullable',
            'ringkasan' => 'nullable',
            'para_pihak' => 'nullable',
            'kronologi' => 'nullable',
            'solusi' => 'nullable',
            'kecamatan' => 'nullable',
            'kegiatan' => 'nullable',
            'pelaksanaan' => 'nullable',
            'bidang' => 'nullable',
            'sumber_info' => 'nullable',
            'nilai_informasi' => 'nullable',
            'w_kegiatan' => 'nullable',
            'id_masyarakat' => 'nullable',
            'lat' => 'required',
            'lng' => 'required',
            'dokumentasi' => 'nullable|image',
        ]);

        $dokumentasi = null;

        if (isset($validatedData['dokumentasi'])) {
            $dokumentasi = $validatedData['dokumentasi']
                ->storeAs('dokumentasi', $user->id . '_' . str_random(40) . '.' .
                    $validatedData['dokumentasi']->extension());
        }
        $data = [
            'id_user' => $user->id,
            'id_indikator' => $validatedData['tipe_laporan'],
            'id_jenis' => $validatedData['jenis'] ?? null,
            'lokasi' => $validatedData['lokasi'] ?? null,
            'uraian' => $validatedData['uraian'] ?? null,
            'id_kategori' => $validatedData['kategori'] ?? null,
            'ringkasan' => $validatedData['ringkasan'] ?? null,
            'para_pihak' => $validatedData['para_pihak'] ?? null,
            'kronologi' => $validatedData['kronologi'] ?? null,
            'solusi' => $validatedData['solusi'] ?? null,
            'id_kecamatan' => $validatedData['kecamatan'] ?? null,
            'kegiatan' => $validatedData['kegiatan'] ?? null,
            'pelaksanaan' => $validatedData['pelaksanaan'] ?? null,
            'id_bidang' => $validatedData['bidang'] ?? null,
            'sumber_info' => $validatedData['sumber_info'] ?? null,
            'nilai_informasi' => $validatedData['nilai_informasi'] ?? null,
            'waktu_kegiatan' => isset($validatedData['w_kegiatan']) ? $validatedData['w_kegiatan'] : Carbon::now(),
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'dokumentasi' => $dokumentasi ?? null,
        ];

        $kegiatan = KegiatanBhabin::create($data);

        if (!$kegiatan)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);
        if($request->id_masyarakat)
            $this->submitMasyarakat($request->id_masyarakat, $kegiatan->id);

        $data = fractal()
            ->item($kegiatan)
            ->transformWith(KegiatanBhabinTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        return response()->json(['success' => true], 201);
    }

    public function createMasyarakat (Request $request) {
        $data = [
            'nik' => $request->nik,
            'nama' => $request->nama,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'suku' => $request->suku,
            'id_agama' => $request->agama,
            'alamat' => $request->alamat,
            'pekerjaan' => $request->pekerjaan,
            'no_hp' => $request->no_hp,
            'status_keluarga' => $request->status_keluarga,
        ];

        $masyarakat = MasyarakatKegiatanBhabin::updateOrCreate(['id' => $request->id], $data);

        return response()->json(['id' => $masyarakat->id], 200);
    }

    public function submitMasyarakat($id_masyarakat, $id_kegiatan)
    {
        for ($i=0; $i < count($id_masyarakat); $i++) { 
            $data = [
                'id_masyarakat' => $id_masyarakat[$i],
                'id_kegiatan_bhabin' => $id_kegiatan,
            ];
            KegiatanMasyarakat::create($data);
        }
    }

    public function searchNikMasyarakat(Request $request)
    {
        $masyarakat = MasyarakatKegiatanBhabin::where("nik", $request->nik)->first();
        if ($masyarakat){
            $data = [
                "ID" => $masyarakat->id,
                "KAB_NAME" => "",
                "NAMA_LGKP" => $masyarakat->nama,
                "KEC_NAME" => "",
                "AGAMA" => $masyarakat->agama->agama,
                "JENIS_PKRJN" => $masyarakat->pekerjaan,
                "TGL_LHR" => $masyarakat->tanggal_lahir,
                "STAT_HBKEL" => $masyarakat->status_keluarga,
                "TMPT_LHR" => $masyarakat->tempat_lahir,
                "ALAMAT" => $masyarakat->alamat,
                "JENIS_KLMIN" => "",
                "SUKU" => $masyarakat->suku,
                "NOHP" => $masyarakat->no_hp
            ];

            return response()->json($data);
        } else {
            $authmaker = function () {
                $client_id = 'resbojonegoro';
                $secret_key = '5dc6126c59a62a941350e31ac29e15e4';
                $cipher = 'aes-256-cbc';
                $hashed_key = hash('sha256', $secret_key);
                $iv = substr($hashed_key, 0, openssl_cipher_iv_length($cipher));
                $time = round(microtime(true) * 1000);
                $token = base64_encode(openssl_encrypt($client_id . '|' . $time, $cipher, $hashed_key, OPENSSL_RAW_DATA, $iv));
                return base64_encode($client_id) . '.' . $token;
            };
            $result = file_get_contents(
                "http://117.103.66.51/api.php?nik={$request->nik}",
                false,
                stream_context_create([
                    'http' => [ 'method' => 'GET',
                        'header' => 'Authorization: Bearer ' . $authmaker() . "\r\n"
                    ]
                ])
            );
            return response()->json(json_decode($result));
        }
    }

    public function getIndikatorJenisKategoriBidang(Request $request)
    {
         $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $tipe = IndikatorKegiatanBhabin::get();
        $jenis = JenisKegiatanBhabin::get();
        $kategori = KategoriKegiatanBhabin::get();
        $bidang = BidangKegiatanBhabin::get();

        return response()->json(compact('tipe', 'jenis', 'kategori', 'bidang'));
    }
}
