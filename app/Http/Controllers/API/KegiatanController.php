<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Kegiatan;
use App\Models\Komentar;
use App\Models\TipeLaporan;
use App\Models\JenisKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Transformers\KomentarTransformer;
use App\Transformers\KegiatanTransformer;
use App\Serializers\DataArraySansIncludeSerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\Cursor;
use App\Events\KegiatanEvent;
use App\Events\KomentarEvent;

class KegiatanController extends Controller
{
    public function upload_laporan(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $validatedData = $request->validate([
            'tipe_laporan' => 'required',
            'judul' => 'nullable',
            'lat' => 'required',
            'lng' => 'required',
            'keterangan' => 'nullable',
            'lokasi' => 'nullable',
            'sasaran' => 'nullable',
            'jenis_laporan' => 'nullable',
            'jenis_kegiatan' => 'nullable',
            'kuat_pers' => 'nullable',
            'hasil' => 'nullable',
            'jml_giat' => 'nullable',
            'jml_tsk' => 'nullable',
            'bb' => 'nullable',
            'perkembangan' => 'nullable',
            'dasar' => 'nullable',
            'modus' => 'nullable',
            'tsk_bb' => 'nullable',
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
            'tipe_laporan' => $validatedData['tipe_laporan'],
            'lat' => $validatedData['lat'],
            'lng' => $validatedData['lng'],
            'lokasi' => $validatedData['lokasi'] ?? null,
            'sasaran' => $validatedData['sasaran'] ?? null,
            'jenis_kegiatan' => $validatedData['jenis_kegiatan'] ?? null,
            'judul' => $validatedData['judul'] ?? null,
            'kuat_pers' => $validatedData['kuat_pers'] ?? null,
            'hasil' => $validatedData['hasil'] ?? null,
            'jml_giat' => $validatedData['jml_giat'] ?? null,
            'jml_tsk' => $validatedData['jml_tsk'] ?? null,
            'bb' => $validatedData['bb'] ?? null,
            'waktu_kegiatan' => Carbon::now(),
            'perkembangan' => $validatedData['perkembangan'] ?? null,
            'dasar' => $validatedData['dasar'] ?? null,
            'keterangan' => $validatedData['keterangan'] ?? null,
            'modus' => $validatedData['modus'] ?? null,
            'tsk_bb' => $validatedData['tsk_bb'] ?? null,
            'dokumentasi' => $dokumentasi ?? null,
        ];
        
        $kegiatan = Kegiatan::create($data);

        if (!$kegiatan)
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);

        // Send notifikasi
        if(!env('APP_DEV')) {
            $this->broadcastNotifikasi($user, $kegiatan);
        }
        

        // Broadcast to monit
        $data = fractal()
            ->item($kegiatan)
            ->transformWith(KegiatanTransformer::class)
            ->parseIncludes(['tipe', 'jenis'])
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->toArray();

        event(new KegiatanEvent($data['data']));

        return response()->json(['success' => true], 201);
    }

    public function detail_laporan(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

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
        list($orderBy, $direction) = explode(':', $request->sort);

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
            return response()->json(['error' => 'Terlarang'], 403);

        $kegiatan = $request->filter == '' ?
            Kegiatan::orderBy($orderBy, $direction) :
            Kegiatan::filter($request->filter)
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
            ->parseIncludes(['tipe', 'jenis'])
            ->transformWith(new KegiatanTransformer(true))
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->respond();
    }

    public function getKomentar(Request $request, Kegiatan $kegiatan)
    {
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
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

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin', 'admin']))
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
        if(!env('APP_DEV')) {
            $this->broadcastNotifikasi($user, $komentar);
        }

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

        if (!in_array($user->jenis_pemilik, ['personil', 'bhabin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);

        $tipe = TipeLaporan::get();
        $jenis = JenisKegiatan::orderBy('status', 'asc')->get();

        return response()->json(compact('tipe', 'jenis'));

    }
}
