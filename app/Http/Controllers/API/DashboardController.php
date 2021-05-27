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

        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 403);
        //Data
        $data = [
        	'personil' => Personil::filterJenisPemilik($user)->count(),
        	'masyarakat' => Masyarakat::count(),
            'kesatuan' => $user->jenis_pemilik == 'admin' ? Kesatuan::count(): Kesatuan::descendantsAndSelf($user->pemilik->id)->count(),
        ];

        return response()->json(['data' => $data]);
    }
}
