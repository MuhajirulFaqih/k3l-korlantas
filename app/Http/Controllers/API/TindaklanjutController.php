<?php

namespace App\Http\Controllers\API;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TindaklanjutController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'keterangan'  => 'required',
            'foto'        => 'required|image',
            'id_kejadian' => 'required',
        ]);

        $name = Str::random(10);
        $foto = $request->file('foto')
                                   ->storeAs('Tindaklanjut',$name. '.' . $request->file('foto')
                                   ->extension());

        $store = array(
            'keterangan'  => $request->keterangan,
            'id_user'     => $request->user()->id,
            'id_kejadian' => $request->id_kejadian,
            'foto'        => $foto,
            'status'      => 1,
        );
        $tindakLanjut = TindakLanjut::insert($store);

        return response()->json('success',200);
    }
}
