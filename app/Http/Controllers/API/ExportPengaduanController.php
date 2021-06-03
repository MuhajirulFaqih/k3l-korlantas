<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\PengaduanTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExportPengaduanController extends Controller
{
    public function index (Request $request) {
        $user = $request->user();

        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);
        $pengaduan = Pengaduan::filteredCetak($request->option, $request->rentang);

        if (count($pengaduan->paginate(1)) === 0)
            return response()->json(['message' => 'Tidak ada content'], 204);

        $paginator = $pengaduan->paginate(10);
        $collection = $paginator->getCollection();


        return fractal()
                ->collection($collection)
                ->transformWith(new PengaduanTransformer(true))
                ->serializeWith(new DataArraySansIncludeSerializer)
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->respond();
    }

    public function cetak (Request $request) {
        $user = $request->user();
        if(!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki akses ke halaman ini'], 403);

        $pengaduan = Pengaduan::filteredCetak($request->option, $request->rentang);
        $pengaduan = $pengaduan->whereNotIn('pengaduan.id', $request->id ?? [])->get();

        $this->generateExcel($pengaduan);
    }

    public function generateExcel($data)
    {
        // Silent is gold
    }

}
