<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Darurat;
use App\Models\Kejadian;
use Illuminate\Http\Request;

class MonitController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if (!in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return response()->json(['error' => 'Anda tidak memiliki aksess ke halaman ini'], 401);

        $data = [
            'kejadian' => Kejadian::paginate($request->limitKejadian),
            'darurat' => Darurat::paginate($request->limitDarurat),
        ];

        return response()->json($data);
    }
}
