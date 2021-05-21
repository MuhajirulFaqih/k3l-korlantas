<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use App\Models\Kesatuan;
use App\Models\Masyarakat;
use App\Models\Personil;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
    	$user = $request->user();

        if (!in_array($user->jenis_pemilik, ['admin']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);
        //Data
        $data = [
        	'personil' => Personil::count(),
        	'masyarakat' => Masyarakat::count(),
        	'kesatuan' => Kesatuan::count(),
        	'informasi' => Informasi::where('aktif', 1)
        							->orderBy('created_at', 'DESC')
        							->paginate(10),
        ];

        return response()->json(['data' => $data]);
    }
}
