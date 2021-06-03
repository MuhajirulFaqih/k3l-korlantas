<?php

/*
 * @Author: Alan
 * @Date: 2018-09-29 09:50:44
 * @Last Modified by: Alan
 * @Last Modified time: 2018-09-29 09:51:45
 */

namespace App\Http\Controllers\API;

use App\Models\Dinas;
use App\Serializers\DataArraySansIncludeSerializer;
use App\Transformers\DinasTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DinasController extends Controller
{
    public function change_state (Request $request)
    {
        $request->validate([
            'giat' => 'required'
        ]);
    }

    public function index(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil', 'admin', 'kesatuan']))
            return response()->json(['Anda tidak memiliki akses ke halaman ini'], 403);

        $dinas = Dinas::get();

        return fractal()
            ->collection($dinas)
            ->transformWith(DinasTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }

    public function getDinas(Request $request){
        $user = $request->user();

        if (!in_array($user->jenis_pemilik, ['personil']))
            return response()->json(['Anda tidak memiliki akses ke halaman ini'], 403);

        $dinas = Dinas::getByStatus($user->jenis_pemilik == 'personil' ? $user->pemilik->jabatan->status_pimpinan : $user->pemilik->personil->jabatan->status_pimpinan)->orderBy('urutan', 'asc')->get();

        return fractal()
            ->collection($dinas)
            ->transformWith(DinasTransformer::class)
            ->serializeWith(DataArraySansIncludeSerializer::class)
            ->respond();
    }
}
