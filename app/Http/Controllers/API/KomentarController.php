<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KomentarController extends Controller
{
    public function komentar(Request $request)
    {
        $request->validate([
            'komentar'    => 'required',
            'kegiatan'    => 'required',
        ]);
        
        $store = array(
            'komentar' => $request->komentar,
            'id_induk' => $request->kegiatan,
            'id_user'  => $request->user()->id,
        );

        \App\Komentar::insert($store);
    return response()->json('success', 200);
    }
}
