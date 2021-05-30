<?php

namespace App\Http\Controllers\API;

use App\Models\Kejadian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\KejadianTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Serializers\DataArraySansIncludeSerializer;
use Illuminate\Support\Str;
use App\Exports\KejadianExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportKejadianController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        list($orderBy, $direction) = explode(':', $request->sort);

        $data = Kejadian::filterLaporan($request->rentang)
                ->filterJenisPemilik($user)
                ->orderBy($orderBy, $direction);

        $paginator = $data->paginate(10);
        $collection = $paginator->getCollection();
        return fractal()
            ->collection($collection)
            ->transformWith(new KejadianTransformer)
            ->serializeWith(new DataArraySansIncludeSerializer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function cetak(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Terlarang'], 403);

        $data = Kejadian::filterLaporan($request->rentang)
                ->filterJenisPemilik($user)
                ->orderBy('w_kejadian', 'desc')->get();

        $kejadian = fractal()
                ->collection($data)
                ->transformWith(new KejadianTransformer)
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->respond();

        $kejadian = json_decode($kejadian->getContent(), true);
        $kejadian = collect($kejadian['data']);
        

        $nama_file = 'kejadian-export-'.date('Y-m-d');
        return Excel::download(new KejadianExport($request, $kejadian), $nama_file . '.xlsx');
    }
}
