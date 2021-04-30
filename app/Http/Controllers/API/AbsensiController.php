<?php

namespace App\Http\Controllers\API;

use App\Models\Absensi;
use App\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\AbsensiTransformer;
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
	    
	    return Excel::download(new AbsensiExport($data), 'Absensi.xlsx');
    }
}
