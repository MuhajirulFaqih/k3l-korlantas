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
use App\Exports\KegiatanExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        list($orderBy, $direction) = explode(':', $request->sort);

        $data = Kegiatan::filterQuickResponse($request->is_quick_response)
                ->filterLaporan($request->rentang, $request->id_jenis)
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
    
    public function jenisQuickResponse(Request $request)
    {
        $jenis = JenisKegiatan::filterQuickResponse()->where('keterangan', 'jenis_kegiatan')->get();
        return response()->json($jenis);
    }

    public function cetak(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $data = Kegiatan::filterQuickResponse($request->is_quick_response)
                ->filterLaporan($request->rentang, $request->id_jenis)
                ->orderBy('waktu_kegiatan', 'desc')->get();

        $kegiatan = fractal()
                ->collection($data)
                ->parseIncludes(['jenis', 'jenis.parent.parent.parent', 'kelurahan'])
                ->transformWith(new KegiatanTransformer)
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->respond();

        $kegiatan = json_decode($kegiatan->getContent(), true);
        $kegiatan = collect($kegiatan['data']);
        

        $nama_file = ($request->is_quick_response ? 'quick-response' : 'kegiatan').'-export-'.date('Y-m-d');
        return Excel::download(new KegiatanExport($request, $kegiatan), $nama_file . '.xlsx');
    }
}
