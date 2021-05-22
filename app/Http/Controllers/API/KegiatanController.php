<?php
namespace App\Http\Controllers\API;

use App\Models\JenisKegiatan;
use App\Models\JenisKegiatanKesatuan;
use App\Models\KegiatanJenisKegiatan;
use App\Models\Kegiatan;
use App\Models\Kesatuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Transformers\KomentarTransformer;
use App\Transformers\KegiatanTransformer;
use App\Transformers\KesatuanTransformer;
use App\Transformers\KegiatanJenisTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use Illuminate\Support\Str;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\Cursor;
use App\Events\KegiatanEvent;
use App\Events\KomentarEvent;

class KegiatanController extends Controller
{
    public function upload_laporan(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $validatedData = $request->validate([
            'id_jenis' => 'nullable',
            'daftar_rekan' => 'nullable',
            'nomor_polisi' => 'nullable',
            'detail' => 'required|min:8',
            'rute_patroli' => 'nullable',
            'dokumentasi' => 'required|image',
            'lat' => 'required',
            'lng' => 'required',
            'id_kelurahan_binmas' => 'nullable',
            'is_quick_response' => 'nullable',
        ]);

        $dokumentasi = null;

        if (isset($validatedData['dokumentasi'])) {
            $dokumentasi = $validatedData['dokumentasi']
                ->storeAs('dokumentasi', $user->id . '_' . Str::random(40) . '.' .
                    $validatedData['dokumentasi']->extension());
        }
        $data = [
            'id_user' => $user->id,
            'daftar_rekan' => $validatedData['daftar_rekan'],
            'nomor_polisi' => $validatedData['nomor_polisi'],
            'detail' => $validatedData['detail'],
            'rute_patroli' => $validatedData['rute_patroli'],
            'waktu_kegiatan' => Carbon::now(),
            'dokumentasi' => $dokumentasi,
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'id_kelurahan_binmas' => $validatedData['id_kelurahan_binmas'],
            'is_quick_response' => $validatedData['is_quick_response']
        ];

        $kegiatan = Kegiatan::create($data);

        if($request->id_jenis) {
            foreach($request->id_jenis as $keyIdJenis => $valueIdJenis) {
                KegiatanJenisKegiatan::create(['id_kegiatan' => $kegiatan->id, 'id_jenis_kegiatan' => $valueIdJenis]);
            }
        }

        if (!$kegiatan)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        // Send notifikasi
        $this->broadcastNotifikasi($user, $kegiatan);


        // Broadcast to monit
        $data = fractal()
            ->item($kegiatan)
            ->transformWith(KegiatanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        event(new KegiatanEvent($data['data']));

        return response()->json(['success' => true], 201);
    }

    public function detail_laporan(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'admin']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        return fractal()
            ->item($kegiatan)
            ->parseIncludes(['tipe', 'jenis'])
            ->transformWith(new KegiatanTransformer(false))
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function list_laporan(Request $request)
    {
        $user = $request->user();
        list($orderBy, $direction) = explode(':', $request->sort ?? 'created_at:desc');

        if (!in_array($user->jenis_pemilik, ['personil', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kegiatan = $request->filter == '' ?
            Kegiatan::with(['user'])->orderBy($orderBy, $direction) :
            Kegiatan::with(['user'])->filter($request->filter)->orderBy($orderBy, $direction);


        $limit = $request->limit != '' ? $request->limit : 10;
        if($limit == 0)
            return null;
        $paginator = $kegiatan->paginate($limit);

        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->parseIncludes(['jenis', 'jenis.parent.parent.parent', 'kelurahan'])
            ->transformWith(new KegiatanTransformer(true))
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function getKomentar(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'admin', 'masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        if ($user->jenis_pemilik == 'masyarakat' && $user->id != $kegiatan->id_user)
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $komentar = $kegiatan->komentar();

        $paginator = $komentar->orderBy('created_at', 'DESC')->paginate(10);
        $collection = $paginator->getCollection();

        return fractal()
            ->collection($collection)
            ->transformWith(KomentarTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function tambahKomentar(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'admin', 'masyarakat']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        if ($user->jenis_pemilik == 'masyarakat' && $user->id != $kegiatan->id_user)
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $validatedData = $request->validate([
            'komentar' => 'required|min:8'
        ]);

        $komentar = $kegiatan->komentar()->create([
            'komentar' => $validatedData['komentar'],
            'id_user' => $user->id
        ]);

        if (!$komentar)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        // Send notifikasi
        $this->broadcastNotifikasi($user, $komentar);

        // Broadcast to monit
        $fractal = fractal()
                ->item($komentar)
                ->transformWith(KomentarTransformer::class)
                ->serializeWith(DataArraySansIncludeSerializer::class);
        // Broadcast to monit
        $data = $fractal->toArray();
        if ($user->jenis_pemilik !== 'admin')
            event(new KomentarEvent($data['data']));

        return $fractal->respond();
    }

    public function getJenisTipe(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $jenis = JenisKegiatan::whereNull('parent_id')->get();

        return fractal()
            ->collection($jenis)
            ->parseIncludes(['children.children.children.children', 'parent.parent.parent'])
            ->transformWith(KegiatanJenisTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
   
    public function getJenisTipeByPersonil(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);
        $id_kesatuan = $user->pemilik->kesatuan->id;
        $id_jenis_kegiatan_by_kesatuan = JenisKegiatanKesatuan::where('id_kesatuan', $id_kesatuan)->pluck('id_jenis_kegiatan');
        $jenis = JenisKegiatan::whereIn('id', $id_jenis_kegiatan_by_kesatuan)->get();

        return fractal()
            ->collection($jenis)
            ->parseIncludes(['children.children.children.children'])
            ->transformWith(KegiatanJenisTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
