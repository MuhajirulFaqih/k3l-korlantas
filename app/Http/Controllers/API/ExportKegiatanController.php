<?php

namespace App\Http\Controllers\API;

use App\Models\Kegiatan;
use App\Models\JenisKegiatan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\KegiatanTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Serializers\DataArraySansIncludeSerializer;
use Illuminate\Support\Str;

class ExportKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        list($orderBy, $direction) = explode(':', $request->sort);

        $data = Kegiatan::with('user', 'jenis', 'user.pemilik')
                ->laporan($request->filter, $request->rentang, $request->id_jenis)
                ->orderBy($orderBy, $direction);

        $paginator = $data->paginate(10);
        $collection = $paginator->getCollection();
        return fractal()
            ->collection($collection)
            ->parseIncludes(['jenis', 'jenis.parent.parent.parent', 'kelurahan'])
            ->transformWith(new KegiatanTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function jenis(Request $request)
    {
        $jenis = JenisKegiatan::filterKegiatan()->where('keterangan', 'jenis_kegiatan')->get();
        return response()->json($jenis);
    }

    public function export(Request $request)
    {
        $kegiatan = Kegiatan::filterkesatuan($request->id_kesatuan)
            ->where('w_kegiatan', '>=', $request->tanggal_mulai)
            ->where('w_kegiatan', '<=', $request->tanggal_selesai)
            ->whereNotIn('id', $request->id_kegiatan)
            ->get();

        $kegiatan = fractal()
            ->collection($kegiatan)
            ->transformWith(KegiatanTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->parseIncludes(['user', 'kesatuan', 'jenis'])
            ->respond();

        $kegiatan = json_decode($kegiatan->getContent(), true);
        $kegiatan = collect($kegiatan['data']);

        $nama_file = $request->tanggal_mulai . '-' . $request->tanggal_selesai;
        return Excel::download(new KegiatanExport($request, $kegiatan), $nama_file . '.xlsx');
    }
}
